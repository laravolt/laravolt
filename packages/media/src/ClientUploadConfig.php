<?php

declare(strict_types=1);

namespace Laravolt\Media;

class ClientUploadConfig
{
    /**
     * Check if client-side upload is enabled
     */
    public static function isEnabled(): bool
    {
        return (bool) config('client-upload.enabled', false);
    }

    /**
     * Get the storage disk for client uploads
     */
    public static function getDisk(): string
    {
        return config('client-upload.disk', 's3');
    }

    /**
     * Get the upload path prefix
     */
    public static function getPathPrefix(): string
    {
        return config('client-upload.path_prefix', 'uploads');
    }

    /**
     * Get presigned URL expiration in minutes
     */
    public static function getUrlExpiration(): int
    {
        return (int) config('client-upload.url_expiration', 60);
    }

    /**
     * Get maximum file size in bytes
     */
    public static function getMaxFileSize(): int
    {
        return (int) config('client-upload.max_file_size', 5 * 1024 * 1024 * 1024);
    }

    /**
     * Get multipart upload threshold in bytes
     */
    public static function getMultipartThreshold(): int
    {
        return (int) config('client-upload.multipart_threshold', 100 * 1024 * 1024);
    }

    /**
     * Get multipart chunk size in bytes
     */
    public static function getMultipartChunkSize(): int
    {
        return (int) config('client-upload.multipart_chunk_size', 10 * 1024 * 1024);
    }

    /**
     * Get maximum concurrent uploads
     */
    public static function getMaxConcurrentUploads(): int
    {
        return (int) config('client-upload.max_concurrent_uploads', 4);
    }

    /**
     * Get allowed MIME types
     */
    public static function getAllowedMimeTypes(): ?array
    {
        return config('client-upload.allowed_mime_types');
    }

    /**
     * Get allowed file extensions
     */
    public static function getAllowedExtensions(): ?array
    {
        return config('client-upload.allowed_extensions');
    }

    /**
     * Check if rate limiting is enabled
     */
    public static function isRateLimitEnabled(): bool
    {
        return (bool) config('client-upload.security.rate_limit.enabled', true);
    }

    /**
     * Get rate limit max attempts
     */
    public static function getRateLimitMaxAttempts(): int
    {
        return (int) config('client-upload.security.rate_limit.max_attempts', 30);
    }

    /**
     * Get rate limit decay minutes
     */
    public static function getRateLimitDecayMinutes(): int
    {
        return (int) config('client-upload.security.rate_limit.decay_minutes', 1);
    }

    /**
     * Check if authentication is required
     */
    public static function requiresAuth(): bool
    {
        return (bool) config('client-upload.security.require_auth', false);
    }

    /**
     * Check if post-upload validation is enabled
     */
    public static function shouldValidateAfterUpload(): bool
    {
        return (bool) config('client-upload.security.validate_after_upload', true);
    }

    /**
     * Check if webhook is enabled
     */
    public static function isWebhookEnabled(): bool
    {
        return (bool) config('client-upload.callbacks.webhook_enabled', false);
    }

    /**
     * Get webhook URL
     */
    public static function getWebhookUrl(): ?string
    {
        return config('client-upload.callbacks.webhook_url');
    }

    /**
     * Get webhook secret
     */
    public static function getWebhookSecret(): ?string
    {
        return config('client-upload.callbacks.webhook_secret');
    }

    /**
     * Get log channel
     */
    public static function getLogChannel(): string
    {
        return config('client-upload.logging.channel', 'daily');
    }

    /**
     * Get log level
     */
    public static function getLogLevel(): string
    {
        return config('client-upload.logging.level', 'info');
    }

    /**
     * Check if successful uploads should be logged
     */
    public static function shouldLogSuccessfulUploads(): bool
    {
        return (bool) config('client-upload.logging.log_successful_uploads', true);
    }

    /**
     * Validate MIME type against configuration
     */
    public static function isMimeTypeValid(string $mimeType): bool
    {
        $allowedTypes = static::getAllowedMimeTypes();

        return $allowedTypes === null || in_array($mimeType, $allowedTypes, true);
    }

    /**
     * Validate file extension against configuration
     */
    public static function isExtensionValid(string $extension): bool
    {
        $allowedExtensions = static::getAllowedExtensions();

        return $allowedExtensions === null || in_array(strtolower($extension), $allowedExtensions, true);
    }

    /**
     * Validate file size against configuration
     */
    public static function isFileSizeValid(int $fileSize): bool
    {
        $maxSize = static::getMaxFileSize();

        return $fileSize <= $maxSize;
    }

    /**
     * Check if file should use multipart upload
     */
    public static function shouldUseMultipart(int $fileSize): bool
    {
        return $fileSize >= static::getMultipartThreshold();
    }

    /**
     * Calculate number of parts for multipart upload
     */
    public static function calculateParts(int $fileSize): int
    {
        $chunkSize = static::getMultipartChunkSize();

        return (int) ceil($fileSize / $chunkSize);
    }

    /**
     * Get frontend-safe configuration
     */
    public static function getFrontendConfig(): array
    {
        return [
            'enabled' => static::isEnabled(),
            'maxFileSize' => static::getMaxFileSize(),
            'multipartThreshold' => static::getMultipartThreshold(),
            'multipartChunkSize' => static::getMultipartChunkSize(),
            'maxConcurrentUploads' => static::getMaxConcurrentUploads(),
            'allowedMimeTypes' => static::getAllowedMimeTypes(),
            'allowedExtensions' => static::getAllowedExtensions(),
            'endpoints' => [
                'initiate' => '/media/client-upload/initiate',
                'presign' => '/media/client-upload/presign',
                'presignPart' => '/media/client-upload/presign-part',
                'complete' => '/media/client-upload/complete-simple',
                'abort' => '/media/client-upload/abort',
            ],
        ];
    }

    /**
     * Get all configuration as array
     */
    public static function toArray(): array
    {
        return [
            'enabled' => static::isEnabled(),
            'disk' => static::getDisk(),
            'pathPrefix' => static::getPathPrefix(),
            'urlExpiration' => static::getUrlExpiration(),
            'maxFileSize' => static::getMaxFileSize(),
            'multipartThreshold' => static::getMultipartThreshold(),
            'multipartChunkSize' => static::getMultipartChunkSize(),
            'maxConcurrentUploads' => static::getMaxConcurrentUploads(),
            'allowedMimeTypes' => static::getAllowedMimeTypes(),
            'allowedExtensions' => static::getAllowedExtensions(),
            'rateLimitEnabled' => static::isRateLimitEnabled(),
            'requiresAuth' => static::requiresAuth(),
            'validateAfterUpload' => static::shouldValidateAfterUpload(),
        ];
    }
}
