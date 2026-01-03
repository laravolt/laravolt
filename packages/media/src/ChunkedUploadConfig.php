<?php

declare(strict_types=1);

namespace Laravolt\Media;

class ChunkedUploadConfig
{
    /**
     * Get the default chunk size in bytes
     */
    public static function getDefaultChunkSize(): int
    {
        return config('chunked-upload.default_chunk_size', 2 * 1024 * 1024);
    }

    /**
     * Get the maximum file size in bytes
     */
    public static function getMaxFileSize(): ?int
    {
        return config('chunked-upload.max_file_size');
    }

    /**
     * Get allowed MIME types
     */
    public static function getAllowedMimeTypes(): ?array
    {
        return config('chunked-upload.allowed_mime_types');
    }

    /**
     * Get the chunks storage disk
     */
    public static function getChunksDisk(): string
    {
        return config('chunked-upload.storage.chunks_disk', 'local');
    }

    /**
     * Get the chunks storage path
     */
    public static function getChunksPath(): string
    {
        return config('chunked-upload.storage.chunks_path', 'chunks');
    }

    /**
     * Get the final storage disk
     */
    public static function getFinalDisk(): string
    {
        return config('chunked-upload.storage.final_disk', 'public');
    }

    /**
     * Get stale hours for cleanup
     */
    public static function getStaleAfterHours(): int
    {
        return config('chunked-upload.cleanup.stale_after_hours', 24);
    }

    /**
     * Check if auto cleanup is enabled
     */
    public static function isAutoCleanupEnabled(): bool
    {
        return config('chunked-upload.cleanup.auto_cleanup', true);
    }

    /**
     * Get cleanup time
     */
    public static function getCleanupTime(): string
    {
        return config('chunked-upload.cleanup.cleanup_time', '02:00');
    }

    /**
     * Check if checksum validation is enabled
     */
    public static function isChecksumValidationEnabled(): bool
    {
        return config('chunked-upload.validation.validate_checksums', false);
    }

    /**
     * Get maximum chunks per file
     */
    public static function getMaxChunksPerFile(): int
    {
        return config('chunked-upload.validation.max_chunks_per_file', 1000);
    }

    /**
     * Get filename validation pattern
     */
    public static function getFilenamePattern(): string
    {
        return config('chunked-upload.validation.filename_pattern', '/^[a-zA-Z0-9._\-\s()]+$/');
    }

    /**
     * Get maximum simultaneous uploads
     */
    public static function getMaxSimultaneousUploads(): int
    {
        return config('chunked-upload.performance.max_simultaneous_uploads', 3);
    }

    /**
     * Get assembly timeout in seconds
     */
    public static function getAssemblyTimeout(): int
    {
        return config('chunked-upload.performance.assembly_timeout', 300);
    }

    /**
     * Get assembly memory limit
     */
    public static function getAssemblyMemoryLimit(): ?string
    {
        return config('chunked-upload.performance.assembly_memory_limit', '256M');
    }

    /**
     * Check if rate limiting is enabled
     */
    public static function isRateLimitEnabled(): bool
    {
        return config('chunked-upload.security.rate_limit.enabled', true);
    }

    /**
     * Get rate limit max attempts
     */
    public static function getRateLimitMaxAttempts(): int
    {
        return config('chunked-upload.security.rate_limit.max_attempts', 60);
    }

    /**
     * Get rate limit decay minutes
     */
    public static function getRateLimitDecayMinutes(): int
    {
        return config('chunked-upload.security.rate_limit.decay_minutes', 1);
    }

    /**
     * Get IP whitelist
     */
    public static function getIpWhitelist(): ?array
    {
        return config('chunked-upload.security.ip_whitelist');
    }

    /**
     * Check if virus scanning is enabled
     */
    public static function isVirusScanEnabled(): bool
    {
        return config('chunked-upload.security.virus_scan.enabled', false);
    }

    /**
     * Get virus scan command
     */
    public static function getVirusScanCommand(): string
    {
        return config('chunked-upload.security.virus_scan.command', 'clamscan --no-summary --infected %s');
    }

    /**
     * Get log channel
     */
    public static function getLogChannel(): string
    {
        return config('chunked-upload.logging.channel', 'daily');
    }

    /**
     * Get log level
     */
    public static function getLogLevel(): string
    {
        return config('chunked-upload.logging.level', 'info');
    }

    /**
     * Check if successful uploads should be logged
     */
    public static function shouldLogSuccessfulUploads(): bool
    {
        return config('chunked-upload.logging.log_successful_uploads', true);
    }

    /**
     * Get default frontend client
     */
    public static function getDefaultClient(): string
    {
        return config('chunked-upload.frontend.default_client', 'resumable');
    }

    /**
     * Get CDN URLs
     */
    public static function getCdnUrls(): array
    {
        return config('chunked-upload.frontend.cdn', []);
    }

    /**
     * Validate file size against configuration
     */
    public static function isFileSizeValid(int $fileSize): bool
    {
        $maxSize = static::getMaxFileSize();
        
        return $maxSize === null || $fileSize <= $maxSize;
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
     * Validate filename against configuration
     */
    public static function isFilenameValid(string $filename): bool
    {
        $pattern = static::getFilenamePattern();
        
        return preg_match($pattern, $filename) === 1;
    }

    /**
     * Get all configuration as array (useful for frontend)
     */
    public static function toArray(): array
    {
        return [
            'defaultChunkSize' => static::getDefaultChunkSize(),
            'maxFileSize' => static::getMaxFileSize(),
            'allowedMimeTypes' => static::getAllowedMimeTypes(),
            'maxChunksPerFile' => static::getMaxChunksPerFile(),
            'maxSimultaneousUploads' => static::getMaxSimultaneousUploads(),
            'defaultClient' => static::getDefaultClient(),
            'cdnUrls' => static::getCdnUrls(),
        ];
    }

    /**
     * Get frontend-safe configuration (excludes sensitive data)
     */
    public static function getFrontendConfig(): array
    {
        return [
            'chunkSize' => static::getDefaultChunkSize(),
            'maxFileSize' => static::getMaxFileSize(),
            'allowedMimeTypes' => static::getAllowedMimeTypes(),
            'maxFiles' => static::getMaxSimultaneousUploads(),
            'client' => static::getDefaultClient(),
            'endpoints' => [
                'upload' => '/media/chunk',
                'status' => '/media/chunk/status',
                'complete' => '/media/chunk/complete',
            ],
        ];
    }
}