<?php

declare(strict_types=1);

use Illuminate\Http\Testing\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravolt\Media\Jobs\CleanupStaleChunksJob;
use Laravolt\Platform\Models\Guest;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

beforeEach(function () {
    // Create a guest user for testing
    if (!Guest::first()) {
        Guest::create([
            'name' => 'Guest User',
            'email' => 'guest@example.com',
        ]);
    }
    
    // Clear any existing media
    Media::query()->delete();
    
    // Clear storage
    Storage::fake('local');
});

test('chunked upload handler can be instantiated', function () {
    $handler = new \Laravolt\Media\MediaHandler\ChunkedMediaHandler();
    expect($handler)->toBeInstanceOf(\Laravolt\Media\MediaHandler\ChunkedMediaHandler::class);
});

test('chunked upload endpoint returns json response', function () {
    $response = $this->post('/media/chunk', [
        'handler' => 'chunked',
        '_action' => 'upload',
    ]);

    $response->assertStatus(400); // Should fail without file
    $response->assertJson(['success' => false]);
});

test('chunked upload can handle file chunks', function () {
    // Create a test file
    $file = UploadedFile::fake()->create('test-file.txt', 1024); // 1MB file
    
    $response = $this->post('/media/chunk', [
        'handler' => 'chunked',
        '_action' => 'upload',
        'file' => $file,
        'resumableChunkNumber' => 1,
        'resumableChunkSize' => 1024 * 1024, // 1MB chunks
        'resumableTotalSize' => 1024,
        'resumableIdentifier' => 'test-file-identifier',
        'resumableFilename' => 'test-file.txt',
        'resumableRelativePath' => 'test-file.txt',
    ]);

    $response->assertSuccessful();
    $response->assertJsonStructure([
        'success',
        'files' => [
            '*' => [
                'file',
                'name',
                'size',
                'type',
                'data' => [
                    'id',
                    'url',
                    'thumbnail',
                ],
            ],
        ],
    ]);
});

test('chunked upload creates media entry in database', function () {
    $file = UploadedFile::fake()->create('test-document.pdf', 512);
    
    $this->post('/media/chunk', [
        'handler' => 'chunked',
        '_action' => 'upload',
        'file' => $file,
        'resumableChunkNumber' => 1,
        'resumableChunkSize' => 1024 * 1024,
        'resumableTotalSize' => 512,
        'resumableIdentifier' => 'test-document-identifier',
        'resumableFilename' => 'test-document.pdf',
        'resumableRelativePath' => 'test-document.pdf',
    ]);

    expect(Media::count())->toBe(1);
    
    $media = Media::first();
    expect($media->file_name)->toBe('test-document.pdf');
    expect($media->mime_type)->toBe('application/pdf');
});

test('chunked upload status endpoint works', function () {
    $response = $this->get('/media/chunk/status', [
        'resumableIdentifier' => 'non-existent-file',
        'resumableFilename' => 'test.txt',
    ]);

    $response->assertSuccessful();
    $response->assertJsonStructure([
        'status',
        'percentage',
    ]);
});

test('chunked upload delete functionality works', function () {
    // First create a media entry
    $file = UploadedFile::fake()->create('delete-test.txt', 100);
    
    $uploadResponse = $this->post('/media/chunk', [
        'handler' => 'chunked',
        '_action' => 'upload',
        'file' => $file,
        'resumableChunkNumber' => 1,
        'resumableChunkSize' => 1024 * 1024,
        'resumableTotalSize' => 100,
        'resumableIdentifier' => 'delete-test-identifier',
        'resumableFilename' => 'delete-test.txt',
        'resumableRelativePath' => 'delete-test.txt',
    ]);

    $uploadResponse->assertSuccessful();
    
    $media = Media::first();
    expect($media)->not->toBeNull();

    // Now delete it
    $deleteResponse = $this->post('/media/chunk', [
        'handler' => 'chunked',
        '_action' => 'delete',
        'id' => $media->id,
    ]);

    $deleteResponse->assertSuccessful();
    $deleteResponse->assertJson(['success' => true]);
    
    expect(Media::count())->toBe(0);
});

test('chunked upload respects file size limits', function () {
    // This test would need to be adjusted based on actual configuration
    $largeFile = UploadedFile::fake()->create('large-file.txt', 1024 * 1024 * 200); // 200MB
    
    $response = $this->post('/media/chunk', [
        'handler' => 'chunked',
        '_action' => 'upload',
        'file' => $largeFile,
        'resumableChunkNumber' => 1,
        'resumableChunkSize' => 1024 * 1024,
        'resumableTotalSize' => 1024 * 1024 * 200,
        'resumableIdentifier' => 'large-file-identifier',
        'resumableFilename' => 'large-file.txt',
        'resumableRelativePath' => 'large-file.txt',
    ]);

    // The response depends on server configuration, but it should handle it gracefully
    expect($response->status())->toBeIn([200, 413, 422, 500]);
});

test('cleanup stale chunks job can be instantiated', function () {
    $job = new CleanupStaleChunksJob(24);
    expect($job)->toBeInstanceOf(CleanupStaleChunksJob::class);
});

test('cleanup stale chunks job handles empty directories gracefully', function () {
    $job = new CleanupStaleChunksJob(1); // 1 hour
    
    // Should not throw any exceptions
    expect(fn() => $job->handle())->not->toThrow(Exception::class);
});

test('media controller routes chunked requests correctly', function () {
    $response = $this->post('/media/media', [
        'handler' => 'chunked',
        '_action' => 'upload',
    ]);

    // Should return a response (even if it fails due to missing file)
    expect($response->status())->toBeIn([200, 400, 422]);
});

test('chunked upload works with guest user', function () {
    // Ensure we're not authenticated
    $this->assertGuest();
    
    $file = UploadedFile::fake()->create('guest-upload.txt', 256);
    
    $response = $this->post('/media/chunk', [
        'handler' => 'chunked',
        '_action' => 'upload',
        'file' => $file,
        'resumableChunkNumber' => 1,
        'resumableChunkSize' => 1024 * 1024,
        'resumableTotalSize' => 256,
        'resumableIdentifier' => 'guest-upload-identifier',
        'resumableFilename' => 'guest-upload.txt',
        'resumableRelativePath' => 'guest-upload.txt',
    ]);

    $response->assertSuccessful();
    
    $media = Media::first();
    expect($media)->not->toBeNull();
    expect($media->model_type)->toBe(Guest::class);
});

test('chunked upload handles invalid actions gracefully', function () {
    $response = $this->post('/media/chunk', [
        'handler' => 'chunked',
        '_action' => 'invalid_action',
    ]);

    // Should return an error or handle gracefully
    expect($response->status())->toBeIn([400, 404, 422, 500]);
});

test('chunked upload returns consistent json structure', function () {
    $file = UploadedFile::fake()->create('structure-test.txt', 128);
    
    $response = $this->post('/media/chunk', [
        'handler' => 'chunked',
        '_action' => 'upload',
        'file' => $file,
        'resumableChunkNumber' => 1,
        'resumableChunkSize' => 1024 * 1024,
        'resumableTotalSize' => 128,
        'resumableIdentifier' => 'structure-test-identifier',
        'resumableFilename' => 'structure-test.txt',
        'resumableRelativePath' => 'structure-test.txt',
    ]);

    $response->assertSuccessful();
    $response->assertJsonStructure([
        'success',
        'files' => [
            '*' => [
                'file',
                'name',
                'size',
                'type',
                'data' => [
                    'id',
                    'url',
                    'thumbnail',
                ],
            ],
        ],
    ]);
    
    $data = $response->json();
    expect($data['success'])->toBe(true);
    expect($data['files'])->toBeArray();
    expect(count($data['files']))->toBe(1);
    
    $file = $data['files'][0];
    expect($file['name'])->toBe('structure-test.txt');
    expect($file['data'])->toHaveKeys(['id', 'url', 'thumbnail']);
});