<?php

declare(strict_types=1);

use Laravolt\Media\ClientUploadConfig;

test('client upload config can check if enabled', function () {
    config(['client-upload.enabled' => true]);
    expect(ClientUploadConfig::isEnabled())->toBe(true);

    config(['client-upload.enabled' => false]);
    expect(ClientUploadConfig::isEnabled())->toBe(false);
});

test('client upload config returns correct disk', function () {
    config(['client-upload.disk' => 's3']);
    expect(ClientUploadConfig::getDisk())->toBe('s3');

    config(['client-upload.disk' => 'r2']);
    expect(ClientUploadConfig::getDisk())->toBe('r2');
});

test('client upload config returns correct path prefix', function () {
    config(['client-upload.path_prefix' => 'uploads']);
    expect(ClientUploadConfig::getPathPrefix())->toBe('uploads');

    config(['client-upload.path_prefix' => 'files']);
    expect(ClientUploadConfig::getPathPrefix())->toBe('files');
});

test('client upload config returns correct url expiration', function () {
    config(['client-upload.url_expiration' => 60]);
    expect(ClientUploadConfig::getUrlExpiration())->toBe(60);

    config(['client-upload.url_expiration' => 120]);
    expect(ClientUploadConfig::getUrlExpiration())->toBe(120);
});

test('client upload config returns correct max file size', function () {
    config(['client-upload.max_file_size' => 100 * 1024 * 1024]);
    expect(ClientUploadConfig::getMaxFileSize())->toBe(100 * 1024 * 1024);
});

test('client upload config returns correct multipart threshold', function () {
    config(['client-upload.multipart_threshold' => 100 * 1024 * 1024]);
    expect(ClientUploadConfig::getMultipartThreshold())->toBe(100 * 1024 * 1024);
});

test('client upload config returns correct multipart chunk size', function () {
    config(['client-upload.multipart_chunk_size' => 10 * 1024 * 1024]);
    expect(ClientUploadConfig::getMultipartChunkSize())->toBe(10 * 1024 * 1024);
});

test('client upload config returns correct max concurrent uploads', function () {
    config(['client-upload.max_concurrent_uploads' => 4]);
    expect(ClientUploadConfig::getMaxConcurrentUploads())->toBe(4);
});

test('client upload config returns allowed mime types', function () {
    $mimeTypes = ['image/jpeg', 'image/png'];
    config(['client-upload.allowed_mime_types' => $mimeTypes]);
    expect(ClientUploadConfig::getAllowedMimeTypes())->toBe($mimeTypes);
});

test('client upload config returns allowed extensions', function () {
    $extensions = ['jpg', 'png'];
    config(['client-upload.allowed_extensions' => $extensions]);
    expect(ClientUploadConfig::getAllowedExtensions())->toBe($extensions);
});

test('client upload config validates mime type correctly', function () {
    config(['client-upload.allowed_mime_types' => ['image/jpeg', 'image/png']]);

    expect(ClientUploadConfig::isMimeTypeValid('image/jpeg'))->toBe(true);
    expect(ClientUploadConfig::isMimeTypeValid('image/png'))->toBe(true);
    expect(ClientUploadConfig::isMimeTypeValid('application/pdf'))->toBe(false);
});

test('client upload config validates mime type when null allows all', function () {
    config(['client-upload.allowed_mime_types' => null]);

    expect(ClientUploadConfig::isMimeTypeValid('image/jpeg'))->toBe(true);
    expect(ClientUploadConfig::isMimeTypeValid('application/pdf'))->toBe(true);
    expect(ClientUploadConfig::isMimeTypeValid('video/mp4'))->toBe(true);
});

test('client upload config validates extension correctly', function () {
    config(['client-upload.allowed_extensions' => ['jpg', 'png']]);

    expect(ClientUploadConfig::isExtensionValid('jpg'))->toBe(true);
    expect(ClientUploadConfig::isExtensionValid('JPG'))->toBe(true);
    expect(ClientUploadConfig::isExtensionValid('png'))->toBe(true);
    expect(ClientUploadConfig::isExtensionValid('pdf'))->toBe(false);
});

test('client upload config validates extension when null allows all', function () {
    config(['client-upload.allowed_extensions' => null]);

    expect(ClientUploadConfig::isExtensionValid('jpg'))->toBe(true);
    expect(ClientUploadConfig::isExtensionValid('pdf'))->toBe(true);
    expect(ClientUploadConfig::isExtensionValid('exe'))->toBe(true);
});

test('client upload config validates file size correctly', function () {
    config(['client-upload.max_file_size' => 100 * 1024 * 1024]); // 100MB

    expect(ClientUploadConfig::isFileSizeValid(50 * 1024 * 1024))->toBe(true);
    expect(ClientUploadConfig::isFileSizeValid(100 * 1024 * 1024))->toBe(true);
    expect(ClientUploadConfig::isFileSizeValid(150 * 1024 * 1024))->toBe(false);
});

test('client upload config determines multipart upload correctly', function () {
    config(['client-upload.multipart_threshold' => 100 * 1024 * 1024]); // 100MB

    expect(ClientUploadConfig::shouldUseMultipart(50 * 1024 * 1024))->toBe(false);
    expect(ClientUploadConfig::shouldUseMultipart(100 * 1024 * 1024))->toBe(true);
    expect(ClientUploadConfig::shouldUseMultipart(150 * 1024 * 1024))->toBe(true);
});

test('client upload config calculates parts correctly', function () {
    config(['client-upload.multipart_chunk_size' => 10 * 1024 * 1024]); // 10MB

    expect(ClientUploadConfig::calculateParts(50 * 1024 * 1024))->toBe(5);
    expect(ClientUploadConfig::calculateParts(55 * 1024 * 1024))->toBe(6);
    expect(ClientUploadConfig::calculateParts(10 * 1024 * 1024))->toBe(1);
});

test('client upload config returns frontend config', function () {
    config([
        'client-upload.enabled' => true,
        'client-upload.max_file_size' => 100 * 1024 * 1024,
        'client-upload.multipart_threshold' => 50 * 1024 * 1024,
        'client-upload.multipart_chunk_size' => 10 * 1024 * 1024,
        'client-upload.max_concurrent_uploads' => 4,
        'client-upload.allowed_mime_types' => ['image/jpeg'],
        'client-upload.allowed_extensions' => ['jpg'],
    ]);

    $config = ClientUploadConfig::getFrontendConfig();

    expect($config)->toHaveKey('enabled');
    expect($config)->toHaveKey('maxFileSize');
    expect($config)->toHaveKey('multipartThreshold');
    expect($config)->toHaveKey('multipartChunkSize');
    expect($config)->toHaveKey('maxConcurrentUploads');
    expect($config)->toHaveKey('allowedMimeTypes');
    expect($config)->toHaveKey('allowedExtensions');
    expect($config)->toHaveKey('endpoints');

    expect($config['enabled'])->toBe(true);
    expect($config['endpoints'])->toHaveKey('initiate');
    expect($config['endpoints'])->toHaveKey('presign');
    expect($config['endpoints'])->toHaveKey('complete');
});

test('client upload config returns full config array', function () {
    $config = ClientUploadConfig::toArray();

    expect($config)->toHaveKey('enabled');
    expect($config)->toHaveKey('disk');
    expect($config)->toHaveKey('pathPrefix');
    expect($config)->toHaveKey('urlExpiration');
    expect($config)->toHaveKey('maxFileSize');
    expect($config)->toHaveKey('multipartThreshold');
    expect($config)->toHaveKey('multipartChunkSize');
    expect($config)->toHaveKey('maxConcurrentUploads');
    expect($config)->toHaveKey('rateLimitEnabled');
    expect($config)->toHaveKey('requiresAuth');
    expect($config)->toHaveKey('validateAfterUpload');
});

test('client upload config security settings work correctly', function () {
    config([
        'client-upload.security.rate_limit.enabled' => true,
        'client-upload.security.rate_limit.max_attempts' => 30,
        'client-upload.security.rate_limit.decay_minutes' => 1,
        'client-upload.security.require_auth' => false,
        'client-upload.security.validate_after_upload' => true,
    ]);

    expect(ClientUploadConfig::isRateLimitEnabled())->toBe(true);
    expect(ClientUploadConfig::getRateLimitMaxAttempts())->toBe(30);
    expect(ClientUploadConfig::getRateLimitDecayMinutes())->toBe(1);
    expect(ClientUploadConfig::requiresAuth())->toBe(false);
    expect(ClientUploadConfig::shouldValidateAfterUpload())->toBe(true);
});

test('client upload config logging settings work correctly', function () {
    config([
        'client-upload.logging.channel' => 'daily',
        'client-upload.logging.level' => 'info',
        'client-upload.logging.log_successful_uploads' => true,
    ]);

    expect(ClientUploadConfig::getLogChannel())->toBe('daily');
    expect(ClientUploadConfig::getLogLevel())->toBe('info');
    expect(ClientUploadConfig::shouldLogSuccessfulUploads())->toBe(true);
});

test('client upload config webhook settings work correctly', function () {
    config([
        'client-upload.callbacks.webhook_enabled' => true,
        'client-upload.callbacks.webhook_url' => 'https://example.com/webhook',
        'client-upload.callbacks.webhook_secret' => 'secret123',
    ]);

    expect(ClientUploadConfig::isWebhookEnabled())->toBe(true);
    expect(ClientUploadConfig::getWebhookUrl())->toBe('https://example.com/webhook');
    expect(ClientUploadConfig::getWebhookSecret())->toBe('secret123');
});
