<?php

declare(strict_types=1);

namespace Laravolt\Media\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Laravolt\Media\ClientUploadConfig;
use Laravolt\Platform\Models\Guest;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ClientUploadController extends Controller
{
    /**
     * Get upload configuration for the frontend
     */
    public function config(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'config' => ClientUploadConfig::getFrontendConfig(),
        ]);
    }

    /**
     * Initiate a new upload session
     * For simple uploads, returns a presigned PUT URL
     * For multipart uploads, initiates the multipart upload and returns uploadId
     *
     * OPTIMIZED: Pre-creates a Media record to get the ID upfront,
     * so the file is uploaded directly to the final path (media_id/filename)
     * eliminating the need for file copy/move on completion.
     */
    public function initiate(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'filename' => 'required|string|max:255',
                'content_type' => 'required|string|max:100',
                'file_size' => 'required|integer|min:1',
            ]);

            // Validate file type and size
            $validationError = $this->validateFile(
                $validated['filename'],
                $validated['content_type'],
                $validated['file_size']
            );

            if ($validationError) {
                return response()->json([
                    'success' => false,
                    'message' => $validationError,
                ], 422);
            }

            // Pre-create Media record to get the ID for the path
            $media = $this->preCreateMediaRecord($validated);
            if (! $media) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to prepare upload',
                ], 500);
            }

            $disk = Storage::disk(ClientUploadConfig::getDisk());

            // Use Spatie's expected path format: {media_id}/{filename}
            $key = $media->id.'/'.$media->file_name;
            $fileSize = (int) $validated['file_size'];

            // Check if we need multipart upload
            if (ClientUploadConfig::shouldUseMultipart($fileSize)) {
                return $this->initiateMultipartUpload($disk, $key, $validated, $media);
            }

            // Simple upload - return presigned PUT URL
            return $this->initiateSimpleUpload($disk, $key, $validated, $media);
        } catch (\Exception $e) {
            Log::error('Client upload initiation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to initiate upload',
            ], 500);
        }
    }

    /**
     * Pre-create a Media record with pending status
     * This allows us to know the media ID upfront and upload directly to the final path
     */
    protected function preCreateMediaRecord(array $validated): ?Media
    {
        try {
            /** @var \Spatie\MediaLibrary\InteractsWithMedia $user */
            $user = auth()->user() ?? Guest::first();

            if (! $user) {
                Log::warning('No user found for media pre-creation');
                return null;
            }

            $disk = ClientUploadConfig::getDisk();
            $filename = $validated['filename'];
            $extension = pathinfo($filename, PATHINFO_EXTENSION);

            // Generate a safe filename
            $safeName = Str::slug(pathinfo($filename, PATHINFO_FILENAME));
            $uniqueFilename = $safeName.'-'.substr(Str::uuid()->toString(), 0, 8).'.'.$extension;

            $media = new Media();
            $media->uuid = Str::uuid()->toString();
            $media->model_type = get_class($user);
            $media->model_id = $user->getKey();
            $media->collection_name = 'default';
            $media->name = pathinfo($filename, PATHINFO_FILENAME);
            $media->file_name = $uniqueFilename;
            $media->mime_type = $validated['content_type'];
            $media->disk = $disk;
            $media->conversions_disk = $disk;
            $media->size = (int) $validated['file_size'];
            $media->manipulations = [];
            $media->custom_properties = [
                'original_filename' => $filename,
                'upload_type' => 'client_side',
                'upload_status' => 'pending', // Mark as pending until confirmed
                'initiated_at' => now()->toIso8601String(),
            ];
            $media->generated_conversions = [];
            $media->responsive_images = [];
            $media->order_column = Media::query()
                ->where('model_type', get_class($user))
                ->where('model_id', $user->getKey())
                ->max('order_column') + 1;

            $media->save();

            return $media;
        } catch (\Exception $e) {
            Log::error('Failed to pre-create media record', [
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Generate a presigned URL for simple PUT upload
     */
    protected function initiateSimpleUpload($disk, string $key, array $validated, Media $media): JsonResponse
    {
        $expiration = now()->addMinutes(ClientUploadConfig::getUrlExpiration());

        // Get the S3 client from the disk
        $client = $disk->getClient();
        $bucket = $disk->getConfig()['bucket'];

        $command = $client->getCommand('PutObject', [
            'Bucket' => $bucket,
            'Key' => $key,
            'ContentType' => $validated['content_type'],
        ]);

        $presignedRequest = $client->createPresignedRequest($command, $expiration);
        $presignedUrl = (string) $presignedRequest->getUri();

        // Generate upload token for confirmation (includes media_id)
        $uploadToken = $this->generateUploadToken($key, $validated, null, $media->id);

        Log::info('Simple upload initiated', [
            'key' => $key,
            'media_id' => $media->id,
            'filename' => $validated['filename'],
            'file_size' => $validated['file_size'],
        ]);

        return response()->json([
            'success' => true,
            'type' => 'simple',
            'upload_url' => $presignedUrl,
            'key' => $key,
            'upload_token' => $uploadToken,
            'media_id' => $media->id, // Return media_id early for client reference
            'expires_at' => $expiration->toIso8601String(),
        ]);
    }

    /**
     * Initiate a multipart upload
     */
    protected function initiateMultipartUpload($disk, string $key, array $validated, Media $media): JsonResponse
    {
        $client = $disk->getClient();
        $bucket = $disk->getConfig()['bucket'];

        // Create multipart upload
        $result = $client->createMultipartUpload([
            'Bucket' => $bucket,
            'Key' => $key,
            'ContentType' => $validated['content_type'],
        ]);

        $uploadId = $result['UploadId'];
        $fileSize = (int) $validated['file_size'];
        $chunkSize = ClientUploadConfig::getMultipartChunkSize();
        $totalParts = ClientUploadConfig::calculateParts($fileSize);

        // Generate upload token for confirmation (includes media_id)
        $uploadToken = $this->generateUploadToken($key, $validated, $uploadId, $media->id);

        Log::info('Multipart upload initiated', [
            'key' => $key,
            'media_id' => $media->id,
            'upload_id' => $uploadId,
            'filename' => $validated['filename'],
            'file_size' => $fileSize,
            'total_parts' => $totalParts,
        ]);

        return response()->json([
            'success' => true,
            'type' => 'multipart',
            'key' => $key,
            'upload_id' => $uploadId,
            'upload_token' => $uploadToken,
            'media_id' => $media->id, // Return media_id early for client reference
            'chunk_size' => $chunkSize,
            'total_parts' => $totalParts,
        ]);
    }

    /**
     * Generate presigned URLs for multipart upload parts
     */
    public function presignPart(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'key' => 'required|string',
                'upload_id' => 'required|string',
                'part_number' => 'required|integer|min:1|max:10000',
            ]);

            $disk = Storage::disk(ClientUploadConfig::getDisk());
            $client = $disk->getClient();
            $bucket = $disk->getConfig()['bucket'];

            $expiration = now()->addMinutes(ClientUploadConfig::getUrlExpiration());

            $command = $client->getCommand('UploadPart', [
                'Bucket' => $bucket,
                'Key' => $validated['key'],
                'UploadId' => $validated['upload_id'],
                'PartNumber' => $validated['part_number'],
            ]);

            $presignedRequest = $client->createPresignedRequest($command, $expiration);
            $presignedUrl = (string) $presignedRequest->getUri();

            return response()->json([
                'success' => true,
                'upload_url' => $presignedUrl,
                'part_number' => $validated['part_number'],
                'expires_at' => $expiration->toIso8601String(),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to generate presigned URL for part', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to generate presigned URL',
            ], 500);
        }
    }

    /**
     * Generate presigned URLs for multiple parts at once
     */
    public function presignParts(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'key' => 'required|string',
                'upload_id' => 'required|string',
                'part_numbers' => 'required|array|min:1|max:100',
                'part_numbers.*' => 'required|integer|min:1|max:10000',
            ]);

            $disk = Storage::disk(ClientUploadConfig::getDisk());
            $client = $disk->getClient();
            $bucket = $disk->getConfig()['bucket'];

            $expiration = now()->addMinutes(ClientUploadConfig::getUrlExpiration());
            $urls = [];

            foreach ($validated['part_numbers'] as $partNumber) {
                $command = $client->getCommand('UploadPart', [
                    'Bucket' => $bucket,
                    'Key' => $validated['key'],
                    'UploadId' => $validated['upload_id'],
                    'PartNumber' => $partNumber,
                ]);

                $presignedRequest = $client->createPresignedRequest($command, $expiration);

                $urls[] = [
                    'part_number' => $partNumber,
                    'upload_url' => (string) $presignedRequest->getUri(),
                ];
            }

            return response()->json([
                'success' => true,
                'urls' => $urls,
                'expires_at' => $expiration->toIso8601String(),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to generate presigned URLs for parts', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to generate presigned URLs',
            ], 500);
        }
    }

    /**
     * Complete a multipart upload
     */
    public function completeMultipart(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'key' => 'required|string',
                'upload_id' => 'required|string',
                'upload_token' => 'required|string',
                'parts' => 'required|array|min:1',
                'parts.*.part_number' => 'required|integer|min:1',
                'parts.*.etag' => 'required|string',
            ]);

            // Verify upload token
            if (! $this->verifyUploadToken($validated['upload_token'], $validated['key'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid upload token',
                ], 403);
            }

            $disk = Storage::disk(ClientUploadConfig::getDisk());
            $client = $disk->getClient();
            $bucket = $disk->getConfig()['bucket'];

            // Format parts for S3
            $parts = array_map(function ($part) {
                return [
                    'PartNumber' => $part['part_number'],
                    'ETag' => $part['etag'],
                ];
            }, $validated['parts']);

            // Sort parts by part number
            // S3/R2 API requires parts to be in ascending order by part number
            usort($parts, fn ($a, $b) => $a['PartNumber'] <=> $b['PartNumber']);

            // Complete the multipart upload
            $result = $client->completeMultipartUpload([
                'Bucket' => $bucket,
                'Key' => $validated['key'],
                'UploadId' => $validated['upload_id'],
                'MultipartUpload' => [
                    'Parts' => $parts,
                ],
            ]);

            // Save to media library
            $media = $this->saveToMediaLibrary($validated['key'], $validated['upload_token']);

            Log::info('Multipart upload completed', [
                'key' => $validated['key'],
                'upload_id' => $validated['upload_id'],
                'media_id' => $media?->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Upload completed successfully',
                'files' => [
                    [
                        'file' => $media?->getUrl() ?? $result['Location'],
                        'name' => $media?->file_name ?? basename($validated['key']),
                        'size' => $media?->size ?? 0,
                        'type' => $media?->mime_type ?? '',
                        'data' => [
                            'id' => $media?->id,
                            'url' => $media?->getUrl() ?? $result['Location'],
                            'thumbnail' => $media?->getUrl() ?? $result['Location'],
                            'key' => $validated['key'],
                        ],
                    ],
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to complete multipart upload', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to complete upload: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Complete a simple upload (confirm and save to media library)
     */
    public function completeSimple(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'key' => 'required|string',
                'upload_token' => 'required|string',
            ]);

            // Verify upload token
            if (! $this->verifyUploadToken($validated['upload_token'], $validated['key'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid upload token',
                ], 403);
            }

            $disk = Storage::disk(ClientUploadConfig::getDisk());

            // Verify file exists
            if (! $disk->exists($validated['key'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'File not found',
                ], 404);
            }

            // Optionally validate file after upload
            if (ClientUploadConfig::shouldValidateAfterUpload()) {
                $validationError = $this->validateUploadedFile($disk, $validated['key'], $validated['upload_token']);
                if ($validationError) {
                    // Delete the file if validation fails
                    $disk->delete($validated['key']);

                    return response()->json([
                        'success' => false,
                        'message' => $validationError,
                    ], 422);
                }
            }

            // Save to media library
            $media = $this->saveToMediaLibrary($validated['key'], $validated['upload_token']);

            Log::info('Simple upload completed', [
                'key' => $validated['key'],
                'media_id' => $media?->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Upload completed successfully',
                'files' => [
                    [
                        'file' => $media?->getUrl() ?? $disk->url($validated['key']),
                        'name' => $media?->file_name ?? basename($validated['key']),
                        'size' => $media?->size ?? $disk->size($validated['key']),
                        'type' => $media?->mime_type ?? $disk->mimeType($validated['key']),
                        'data' => [
                            'id' => $media?->id,
                            'url' => $media?->getUrl() ?? $disk->url($validated['key']),
                            'thumbnail' => $media?->getUrl() ?? $disk->url($validated['key']),
                            'key' => $validated['key'],
                        ],
                    ],
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to complete simple upload', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to complete upload: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Abort a multipart upload or cleanup a simple upload
     */
    public function abort(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'key' => 'nullable|string',
                'upload_id' => 'nullable|string',
                'upload_token' => 'nullable|string', // Optional: to clean up media record
            ]);

            // For multipart uploads, abort on S3
            if (! empty($validated['upload_id']) && ! empty($validated['key'])) {
                $disk = Storage::disk(ClientUploadConfig::getDisk());
                $client = $disk->getClient();
                $bucket = $disk->getConfig()['bucket'];

                $client->abortMultipartUpload([
                    'Bucket' => $bucket,
                    'Key' => $validated['key'],
                    'UploadId' => $validated['upload_id'],
                ]);

                Log::info('Multipart upload aborted', [
                    'key' => $validated['key'],
                    'upload_id' => $validated['upload_id'],
                ]);
            }

            // Clean up pre-created media record if token provided
            if (! empty($validated['upload_token'])) {
                $this->cleanupPendingMedia($validated['upload_token']);
            }

            return response()->json([
                'success' => true,
                'message' => 'Upload aborted',
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to abort upload', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to abort upload',
            ], 500);
        }
    }

    /**
     * Clean up a pending media record (for aborted/failed uploads)
     */
    protected function cleanupPendingMedia(string $uploadToken): void
    {
        try {
            $tokenData = $this->decodeUploadToken($uploadToken);
            if (! $tokenData || empty($tokenData['media_id'])) {
                return;
            }

            $media = Media::find($tokenData['media_id']);
            if ($media) {
                $uploadStatus = $media->custom_properties['upload_status'] ?? null;
                // Only delete if still pending
                if ($uploadStatus === 'pending') {
                    $media->delete();
                    Log::info('Cleaned up pending media record', ['media_id' => $tokenData['media_id']]);
                }
            }
        } catch (\Exception $e) {
            Log::warning('Failed to cleanup pending media', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Generate a unique key for the uploaded file
     */
    protected function generateKey(string $filename): string
    {
        $prefix = ClientUploadConfig::getPathPrefix();
        $date = now()->format('Y/m/d');
        $uuid = Str::uuid()->toString();
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        $safeName = Str::slug(pathinfo($filename, PATHINFO_FILENAME));

        return sprintf('%s/%s/%s-%s.%s', $prefix, $date, $safeName, substr($uuid, 0, 8), $extension);
    }

    /**
     * Validate file before upload
     */
    protected function validateFile(string $filename, string $contentType, int $fileSize): ?string
    {
        // Validate file size
        if (! ClientUploadConfig::isFileSizeValid($fileSize)) {
            return sprintf(
                'File size exceeds maximum allowed size of %s bytes',
                ClientUploadConfig::getMaxFileSize()
            );
        }

        // Validate MIME type
        if (! ClientUploadConfig::isMimeTypeValid($contentType)) {
            return 'File type not allowed';
        }

        // Validate extension
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        if (! ClientUploadConfig::isExtensionValid($extension)) {
            return 'File extension not allowed';
        }

        return null;
    }

    /**
     * Validate file after upload
     */
    protected function validateUploadedFile($disk, string $key, string $uploadToken): ?string
    {
        // Get token data
        $tokenData = $this->decodeUploadToken($uploadToken);
        if (! $tokenData) {
            return 'Invalid upload token';
        }

        // Verify file size matches
        $actualSize = $disk->size($key);
        if ($actualSize !== (int) $tokenData['file_size']) {
            return 'File size mismatch';
        }

        // Verify MIME type
        $actualMimeType = $disk->mimeType($key);
        if ($actualMimeType && $actualMimeType !== $tokenData['content_type']) {
            // Allow some flexibility for MIME type detection
            if (! ClientUploadConfig::isMimeTypeValid($actualMimeType)) {
                return 'File type mismatch';
            }
        }

        return null;
    }

    /**
     * Generate an upload token containing file metadata
     */
    protected function generateUploadToken(string $key, array $fileData, ?string $uploadId = null, ?int $mediaId = null): string
    {
        $data = [
            'key' => $key,
            'filename' => $fileData['filename'],
            'content_type' => $fileData['content_type'],
            'file_size' => $fileData['file_size'],
            'upload_id' => $uploadId,
            'media_id' => $mediaId,
            'user_id' => auth()->id(),
            'expires_at' => now()->addMinutes(ClientUploadConfig::getUrlExpiration() + 30)->timestamp,
        ];

        $json = json_encode($data, JSON_UNESCAPED_SLASHES);
        $signature = hash_hmac('sha256', $json, config('app.key'));

        // Use '|' as delimiter since '.' can appear in filenames/paths
        return base64_encode($json.'|'.$signature);
    }

    /**
     * Verify and decode an upload token
     */
    protected function verifyUploadToken(string $token, string $key): bool
    {
        Log::debug('verifyUploadToken: Starting verification', [
            'token_length' => strlen($token),
            'key' => $key,
        ]);

        $tokenData = $this->decodeUploadToken($token);
        if (! $tokenData) {
            Log::debug('verifyUploadToken: Token decode failed');
            return false;
        }

        Log::debug('verifyUploadToken: Token decoded successfully', [
            'token_key' => $tokenData['key'] ?? 'NOT SET',
            'request_key' => $key,
            'keys_match' => ($tokenData['key'] ?? '') === $key,
        ]);

        // Verify key matches
        if ($tokenData['key'] !== $key) {
            Log::debug('verifyUploadToken: Key mismatch', [
                'token_key' => $tokenData['key'],
                'request_key' => $key,
                'token_key_hex' => bin2hex($tokenData['key']),
                'request_key_hex' => bin2hex($key),
            ]);
            return false;
        }

        // Verify token hasn't expired
        if ($tokenData['expires_at'] < now()->timestamp) {
            Log::debug('verifyUploadToken: Token expired', [
                'expires_at' => $tokenData['expires_at'],
                'now' => now()->timestamp,
            ]);
            return false;
        }

        Log::debug('verifyUploadToken: Verification successful');
        return true;
    }

    /**
     * Decode an upload token
     */
    protected function decodeUploadToken(string $token): ?array
    {
        try {
            Log::debug('decodeUploadToken: Starting decode', [
                'token_first_50' => substr($token, 0, 50),
                'token_length' => strlen($token),
            ]);

            $decoded = base64_decode($token);
            Log::debug('decodeUploadToken: Base64 decoded', [
                'decoded_length' => strlen($decoded),
                'decoded_first_100' => substr($decoded, 0, 100),
            ]);

            // Use '|' as delimiter (fallback to '.' for backward compatibility)
            if (strpos($decoded, '|') !== false) {
                $lastDelimiterPos = strrpos($decoded, '|');
            } else {
                // Backward compatibility: use last '.' for old tokens
                $lastDelimiterPos = strrpos($decoded, '.');
            }

            if ($lastDelimiterPos === false) {
                Log::debug('decodeUploadToken: No delimiter found');
                return null;
            }

            $json = substr($decoded, 0, $lastDelimiterPos);
            $signature = substr($decoded, $lastDelimiterPos + 1);

            Log::debug('decodeUploadToken: Split by delimiter', [
                'json_length' => strlen($json),
                'signature_length' => strlen($signature),
            ]);

            Log::debug('decodeUploadToken: Extracted parts', [
                'json_length' => strlen($json),
                'signature_length' => strlen($signature),
                'json_preview' => substr($json, 0, 100),
            ]);

            // Verify signature
            $expectedSignature = hash_hmac('sha256', $json, config('app.key'));
            $signatureMatch = hash_equals($expectedSignature, $signature);

            Log::debug('decodeUploadToken: Signature verification', [
                'expected_signature' => $expectedSignature,
                'actual_signature' => $signature,
                'match' => $signatureMatch,
            ]);

            if (! $signatureMatch) {
                Log::debug('decodeUploadToken: Signature mismatch');
                return null;
            }

            $data = json_decode($json, true);
            Log::debug('decodeUploadToken: JSON decoded', [
                'data' => $data,
            ]);

            return $data;
        } catch (\Exception $e) {
            Log::debug('decodeUploadToken: Exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return null;
        }
    }

    /**
     * Complete the media record after upload
     *
     * OPTIMIZED: Since we pre-create the media record in initiate(),
     * this method just updates the status. No file operations needed!
     * The file is already at the correct path: {media_id}/{filename}
     */
    protected function saveToMediaLibrary(string $key, string $uploadToken)
    {
        try {
            $tokenData = $this->decodeUploadToken($uploadToken);
            if (! $tokenData) {
                Log::error('Failed to decode upload token');
                return null;
            }

            $mediaId = $tokenData['media_id'] ?? null;
            if (! $mediaId) {
                Log::error('No media_id in upload token');
                return null;
            }

            // Find the pre-created media record
            $media = Media::find($mediaId);
            if (! $media) {
                Log::error('Media record not found', ['media_id' => $mediaId]);
                return null;
            }

            // Update the media record to mark upload as complete
            $customProperties = $media->custom_properties ?? [];
            $customProperties['upload_status'] = 'completed';
            $customProperties['completed_at'] = now()->toIso8601String();
            unset($customProperties['initiated_at']); // Clean up

            $media->custom_properties = $customProperties;
            $media->save();

            Log::info('Media upload completed', [
                'media_id' => $media->id,
                'key' => $key,
            ]);

            return $media;
        } catch (\Exception $e) {
            Log::error('Failed to complete media record', [
                'error' => $e->getMessage(),
                'key' => $key,
                'trace' => $e->getTraceAsString(),
            ]);

            return null;
        }
    }
}
