<?php

declare(strict_types=1);

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
    /** @var TestCase */
    $test = $this;
    $response = $test->get('/media/client-upload/config');

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
    /** @var TestCase */
    $test = $this;
    $response = $test->get('/media/client-upload/config');

    $data = $response->json();

    expect($data['config']['endpoints'])->toHaveKey('initiate');
    expect($data['config']['endpoints'])->toHaveKey('presign');
    expect($data['config']['endpoints'])->toHaveKey('complete');
});

test('client upload initiate requires filename', function () {
    /** @var TestCase */
    $test = $this;
    $response = $test->postJson('/media/client-upload/initiate', [
        'content_type' => 'image/jpeg',
        'file_size' => 1024,
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['filename']);
});

test('client upload initiate requires content_type', function () {
    /** @var TestCase */
    $test = $this;
    $response = $test->postJson('/media/client-upload/initiate', [
        'filename' => 'test.jpg',
        'file_size' => 1024,
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['content_type']);
});

test('client upload initiate requires file_size', function () {
    /** @var TestCase */
    $test = $this;
    $response = $test->postJson('/media/client-upload/initiate', [
        'filename' => 'test.jpg',
        'content_type' => 'image/jpeg',
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['file_size']);
});

test('client upload initiate validates file size limit', function () {
    config(['client-upload.max_file_size' => 1024]); // 1KB limit

    /** @var TestCase */
    $test = $this;
    $response = $test->postJson('/media/client-upload/initiate', [
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

    /** @var TestCase */
    $test = $this;
    $response = $test->postJson('/media/client-upload/initiate', [
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

    /** @var TestCase */
    $test = $this;
    $response = $test->postJson('/media/client-upload/initiate', [
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
    /** @var TestCase */
    $test = $this;
    $response = $test->postJson('/media/client-upload/presign-part', [
        'upload_id' => 'test-upload-id',
        'part_number' => 1,
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['key']);
});

test('client upload presign part requires upload_id', function () {
    /** @var TestCase */
    $test = $this;
    $response = $test->postJson('/media/client-upload/presign-part', [
        'key' => 'uploads/test.jpg',
        'part_number' => 1,
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['upload_id']);
});

test('client upload presign part requires part_number', function () {
    /** @var TestCase */
    $test = $this;
    $response = $test->postJson('/media/client-upload/presign-part', [
        'key' => 'uploads/test.jpg',
        'upload_id' => 'test-upload-id',
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['part_number']);
});

test('client upload presign part validates part_number range', function () {
    /** @var TestCase */
    $test = $this;
    $response = $test->postJson('/media/client-upload/presign-part', [
        'key' => 'uploads/test.jpg',
        'upload_id' => 'test-upload-id',
        'part_number' => 0, // Invalid - must be >= 1
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['part_number']);

    /** @var TestCase */
    $test = $this;
    $response = $test->postJson('/media/client-upload/presign-part', [
        'key' => 'uploads/test.jpg',
        'upload_id' => 'test-upload-id',
        'part_number' => 10001, // Invalid - must be <= 10000
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['part_number']);
});

test('client upload presign parts requires part_numbers array', function () {
    /** @var TestCase */
    $test = $this;
    $response = $test->postJson('/media/client-upload/presign-parts', [
        'key' => 'uploads/test.jpg',
        'upload_id' => 'test-upload-id',
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['part_numbers']);
});

test('client upload complete multipart requires key', function () {
    /** @var TestCase */
    $test = $this;
    $response = $test->postJson('/media/client-upload/complete-multipart', [
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
    /** @var TestCase */
    $test = $this;
    $response = $test->postJson('/media/client-upload/complete-multipart', [
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
    /** @var TestCase */
    $test = $this;
    $response = $test->postJson('/media/client-upload/complete-multipart', [
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
    /** @var TestCase */
    $test = $this;
    $response = $test->postJson('/media/client-upload/complete-multipart', [
        'key' => 'uploads/test.jpg',
        'upload_id' => 'test-upload-id',
        'upload_token' => 'test-token',
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['parts']);
});

test('client upload complete simple requires key', function () {
    /** @var TestCase */
    $test = $this;
    $response = $test->postJson('/media/client-upload/complete-simple', [
        'upload_token' => 'test-token',
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['key']);
});

test('client upload complete simple requires upload_token', function () {
    /** @var TestCase */
    $test = $this;
    $response = $test->postJson('/media/client-upload/complete-simple', [
        'key' => 'uploads/test.jpg',
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['upload_token']);
});

test('client upload abort without a multipart upload is a no-op', function () {
    // key and upload_id are only needed to abort a multipart upload on S3.
    /** @var TestCase */
    $test = $this;

    $test->postJson('/media/client-upload/abort', ['upload_id' => 'test-upload-id'])
        ->assertOk()
        ->assertJson(['success' => true]);

    $test->postJson('/media/client-upload/abort', ['key' => 'uploads/test.jpg'])
        ->assertOk()
        ->assertJson(['success' => true]);
});

test('client upload complete simple rejects invalid token', function () {
    /** @var TestCase */
    $test = $this;
    $response = $test->postJson('/media/client-upload/complete-simple', [
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
    /** @var TestCase */
    $test = $this;
    $response = $test->postJson('/media/client-upload/complete-multipart', [
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

function clientUploadTestToken(array $data = []): string
{
    $json = json_encode(array_merge([
        'key' => '1/report-abcd1234.pdf',
        'filename' => 'report.pdf',
        'content_type' => 'application/pdf',
        'file_size' => 1024,
        'upload_id' => 'upload-1',
        'media_id' => null,
        'user_id' => null,
        'expires_at' => now()->addHour()->timestamp,
    ], $data), JSON_UNESCAPED_SLASHES);

    return base64_encode($json.'|'.hash_hmac('sha256', $json, config('app.key')));
}

test('client upload presign part requires the upload token', function () {
    $this->postJson('/media/client-upload/presign-part', [
        'key' => '1/report-abcd1234.pdf',
        'upload_id' => 'upload-1',
        'part_number' => 1,
    ])->assertStatus(422)->assertJsonValidationErrors(['upload_token']);
});

test('client upload presign part only accepts the token of that upload', function (array $request) {
    $this->postJson('/media/client-upload/presign-part', [
        'part_number' => 1,
        'upload_token' => clientUploadTestToken(),
        ...$request,
    ])->assertStatus(403)->assertJson(['success' => false, 'message' => 'Invalid upload token']);
})->with([
    'another key' => [['key' => '2/other-abcd1234.pdf', 'upload_id' => 'upload-1']],
    'another upload id' => [['key' => '1/report-abcd1234.pdf', 'upload_id' => 'upload-2']],
]);

test('client upload presign parts only accepts the token of that upload', function () {
    $this->postJson('/media/client-upload/presign-parts', [
        'key' => '1/report-abcd1234.pdf',
        'upload_id' => 'upload-2',
        'part_numbers' => [1, 2],
        'upload_token' => clientUploadTestToken(),
    ])->assertStatus(403)->assertJson(['success' => false, 'message' => 'Invalid upload token']);
});

test('client upload token is bound to the uploader', function () {
    $this->postJson('/media/client-upload/presign-part', [
        'key' => '1/report-abcd1234.pdf',
        'upload_id' => 'upload-1',
        'part_number' => 1,
        'upload_token' => clientUploadTestToken(['user_id' => 999]),
    ])->assertStatus(403);
});

test('client upload abort of a multipart upload needs its token', function (array $extra) {
    $this->postJson('/media/client-upload/abort', [
        'key' => '1/report-abcd1234.pdf',
        'upload_id' => 'upload-1',
        ...$extra,
    ])->assertStatus(403)->assertJson(['success' => false, 'message' => 'Invalid upload token']);
})->with([
    'no token' => fn () => [],
    'token of another upload' => fn () => ['upload_token' => clientUploadTestToken(['upload_id' => 'upload-2'])],
]);

test('client upload honours require_auth for guests', function () {
    config(['client-upload.security.require_auth' => true]);

    $this->postJson('/media/client-upload/initiate', [
        'filename' => 'photo.jpg',
        'content_type' => 'image/jpeg',
        'file_size' => 1024,
    ])->assertStatus(401)->assertJson(['success' => false]);

    $this->postJson('/media/client-upload/abort', [])->assertStatus(401);
});
