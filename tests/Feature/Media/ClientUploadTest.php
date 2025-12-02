<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Storage;
use Laravolt\Media\ClientUploadConfig;
use Laravolt\Platform\Models\Guest;

beforeEach(function () {
    // Create a guest user for testing
    if (! Guest::first()) {
        Guest::create([
            'name' => 'Guest User',
            'email' => 'guest@example.com',
        ]);
    }

    // Enable client upload for testing
    config(['client-upload.enabled' => true]);
});

test('client upload config endpoint returns json', function () {
    $response = $this->get('/media/client-upload/config');

    $response->assertStatus(200);
    $response->assertJsonStructure([
        'success',
        'config' => [
            'enabled',
            'maxFileSize',
            'multipartThreshold',
            'multipartChunkSize',
            'maxConcurrentUploads',
            'endpoints',
        ],
    ]);
});

test('client upload config endpoint returns correct endpoints', function () {
    $response = $this->get('/media/client-upload/config');

    $data = $response->json();

    expect($data['config']['endpoints'])->toHaveKey('initiate');
    expect($data['config']['endpoints'])->toHaveKey('presign');
    expect($data['config']['endpoints'])->toHaveKey('complete');
});

test('client upload initiate requires filename', function () {
    $response = $this->postJson('/media/client-upload/initiate', [
        'content_type' => 'image/jpeg',
        'file_size' => 1024,
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['filename']);
});

test('client upload initiate requires content_type', function () {
    $response = $this->postJson('/media/client-upload/initiate', [
        'filename' => 'test.jpg',
        'file_size' => 1024,
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['content_type']);
});

test('client upload initiate requires file_size', function () {
    $response = $this->postJson('/media/client-upload/initiate', [
        'filename' => 'test.jpg',
        'content_type' => 'image/jpeg',
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['file_size']);
});

test('client upload initiate validates file size limit', function () {
    config(['client-upload.max_file_size' => 1024]); // 1KB limit

    $response = $this->postJson('/media/client-upload/initiate', [
        'filename' => 'test.jpg',
        'content_type' => 'image/jpeg',
        'file_size' => 2048, // 2KB - exceeds limit
    ]);

    $response->assertStatus(422);
    $response->assertJson([
        'success' => false,
    ]);
});

test('client upload initiate validates mime type', function () {
    config(['client-upload.allowed_mime_types' => ['image/jpeg', 'image/png']]);

    $response = $this->postJson('/media/client-upload/initiate', [
        'filename' => 'test.exe',
        'content_type' => 'application/x-msdownload',
        'file_size' => 1024,
    ]);

    $response->assertStatus(422);
    $response->assertJson([
        'success' => false,
        'message' => 'File type not allowed',
    ]);
});

test('client upload initiate validates extension', function () {
    config([
        'client-upload.allowed_mime_types' => null, // Allow all mime types
        'client-upload.allowed_extensions' => ['jpg', 'png'],
    ]);

    $response = $this->postJson('/media/client-upload/initiate', [
        'filename' => 'test.exe',
        'content_type' => 'image/jpeg',
        'file_size' => 1024,
    ]);

    $response->assertStatus(422);
    $response->assertJson([
        'success' => false,
        'message' => 'File extension not allowed',
    ]);
});

test('client upload presign part requires key', function () {
    $response = $this->postJson('/media/client-upload/presign-part', [
        'upload_id' => 'test-upload-id',
        'part_number' => 1,
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['key']);
});

test('client upload presign part requires upload_id', function () {
    $response = $this->postJson('/media/client-upload/presign-part', [
        'key' => 'uploads/test.jpg',
        'part_number' => 1,
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['upload_id']);
});

test('client upload presign part requires part_number', function () {
    $response = $this->postJson('/media/client-upload/presign-part', [
        'key' => 'uploads/test.jpg',
        'upload_id' => 'test-upload-id',
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['part_number']);
});

test('client upload presign part validates part_number range', function () {
    $response = $this->postJson('/media/client-upload/presign-part', [
        'key' => 'uploads/test.jpg',
        'upload_id' => 'test-upload-id',
        'part_number' => 0, // Invalid - must be >= 1
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['part_number']);

    $response = $this->postJson('/media/client-upload/presign-part', [
        'key' => 'uploads/test.jpg',
        'upload_id' => 'test-upload-id',
        'part_number' => 10001, // Invalid - must be <= 10000
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['part_number']);
});

test('client upload presign parts requires part_numbers array', function () {
    $response = $this->postJson('/media/client-upload/presign-parts', [
        'key' => 'uploads/test.jpg',
        'upload_id' => 'test-upload-id',
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['part_numbers']);
});

test('client upload complete multipart requires key', function () {
    $response = $this->postJson('/media/client-upload/complete-multipart', [
        'upload_id' => 'test-upload-id',
        'upload_token' => 'test-token',
        'parts' => [
            ['part_number' => 1, 'etag' => 'test-etag'],
        ],
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['key']);
});

test('client upload complete multipart requires upload_id', function () {
    $response = $this->postJson('/media/client-upload/complete-multipart', [
        'key' => 'uploads/test.jpg',
        'upload_token' => 'test-token',
        'parts' => [
            ['part_number' => 1, 'etag' => 'test-etag'],
        ],
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['upload_id']);
});

test('client upload complete multipart requires upload_token', function () {
    $response = $this->postJson('/media/client-upload/complete-multipart', [
        'key' => 'uploads/test.jpg',
        'upload_id' => 'test-upload-id',
        'parts' => [
            ['part_number' => 1, 'etag' => 'test-etag'],
        ],
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['upload_token']);
});

test('client upload complete multipart requires parts', function () {
    $response = $this->postJson('/media/client-upload/complete-multipart', [
        'key' => 'uploads/test.jpg',
        'upload_id' => 'test-upload-id',
        'upload_token' => 'test-token',
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['parts']);
});

test('client upload complete simple requires key', function () {
    $response = $this->postJson('/media/client-upload/complete-simple', [
        'upload_token' => 'test-token',
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['key']);
});

test('client upload complete simple requires upload_token', function () {
    $response = $this->postJson('/media/client-upload/complete-simple', [
        'key' => 'uploads/test.jpg',
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['upload_token']);
});

test('client upload abort requires key', function () {
    $response = $this->postJson('/media/client-upload/abort', [
        'upload_id' => 'test-upload-id',
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['key']);
});

test('client upload abort requires upload_id', function () {
    $response = $this->postJson('/media/client-upload/abort', [
        'key' => 'uploads/test.jpg',
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['upload_id']);
});

test('client upload complete simple rejects invalid token', function () {
    $response = $this->postJson('/media/client-upload/complete-simple', [
        'key' => 'uploads/test.jpg',
        'upload_token' => 'invalid-token',
    ]);

    $response->assertStatus(403);
    $response->assertJson([
        'success' => false,
        'message' => 'Invalid upload token',
    ]);
});

test('client upload complete multipart rejects invalid token', function () {
    $response = $this->postJson('/media/client-upload/complete-multipart', [
        'key' => 'uploads/test.jpg',
        'upload_id' => 'test-upload-id',
        'upload_token' => 'invalid-token',
        'parts' => [
            ['part_number' => 1, 'etag' => 'test-etag'],
        ],
    ]);

    $response->assertStatus(403);
    $response->assertJson([
        'success' => false,
        'message' => 'Invalid upload token',
    ]);
});
