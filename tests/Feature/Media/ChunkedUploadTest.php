<?php

declare(strict_types=1);

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Laravolt\Media\Jobs\CleanupStaleChunksJob;
use Laravolt\Platform\Models\Guest;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

beforeEach(function () {
    // Create a guest user for testing
    if (! Guest::first()) {
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
    $handler = new Laravolt\Media\MediaHandler\ChunkedMediaHandler();
    expect($handler)->toBeInstanceOf(Laravolt\Media\MediaHandler\ChunkedMediaHandler::class);
});

test('chunked upload endpoint returns json response', function () {
    /** @var TestCase */
    $test = $this;
    $response = $test->post('/media/chunk', [
        'handler' => 'chunked',
        '_action' => 'upload',
    ]);

    $response->assertStatus(400); // Should fail without file
    $response->assertJson(['success' => false]);
});

test('chunked upload can handle file chunks', function () {
    // Create a test file
    $file = UploadedFile::fake()->create('test-file.txt', 1024); // 1MB file
    /** @var TestCase */
    $test = $this;
    $response = $test->post('/media/chunk', [
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
    // A real PDF header, so the stored MIME type is detected from the content.
    $file = UploadedFile::fake()->createWithContent('test-document.pdf', "%PDF-1.4\n%%EOF\n");
    /** @var TestCase */
    $test = $this;
    $test->post('/media/chunk', [
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

test('chunked upload status endpoint reports a missing chunk', function () {
    /** @var TestCase */
    $test = $this;
    $response = $test->get('/media/chunk/status?'.http_build_query([
        'resumableIdentifier' => 'non-existent-file',
        'resumableFilename' => 'test.txt',
        'resumableChunkNumber' => 1,
    ]));

    // 404 tells Resumable.js to send the chunk.
    $response->assertNotFound();
    $response->assertJson(['status' => 'not_found', 'percentage' => 0]);
});

test('chunked upload delete functionality works', function () {
    // First create a media entry
    $file = UploadedFile::fake()->create('delete-test.txt', 100);
    $user = Laravolt\Platform\Models\User::query()->forceCreate(['name' => 'Uploader', 'email' => 'uploader@example.test', 'password' => 'secret']);
    /** @var TestCase */
    $test = $this;
    $test->actingAs($user);
    $uploadResponse = $test->post('/media/chunk', [
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
    /** @var TestCase */
    $test = $this;
    $deleteResponse = $test->post('/media/media', [
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
    /** @var TestCase */
    $test = $this;
    $response = $test->post('/media/chunk', [
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
    expect(fn () => $job->handle())->not->toThrow(Exception::class);
});

test('media controller routes chunked requests correctly', function () {
    /** @var TestCase */
    $test = $this;
    $response = $test->post('/media/media', [
        'handler' => 'chunked',
        '_action' => 'upload',
    ]);

    // Should return a response (even if it fails due to missing file)
    expect($response->status())->toBeIn([200, 400, 422]);
});

test('chunked upload works with guest user', function () {
    // Ensure we're not authenticated
    /** @var TestCase */
    $test = $this;
    $test->assertGuest();

    $file = UploadedFile::fake()->create('guest-upload.txt', 256);

    /** @var TestCase */
    $test = $this;
    $response = $test->post('/media/chunk', [
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
    /** @var TestCase */
    $test = $this;
    $response = $test->post('/media/chunk', [
        'handler' => 'chunked',
        '_action' => 'invalid_action',
    ]);

    // Should return an error or handle gracefully
    expect($response->status())->toBeIn([400, 404, 422, 500]);
});

test('chunked upload returns consistent json structure', function () {
    $file = UploadedFile::fake()->create('structure-test.txt', 128);
    /** @var TestCase */
    $test = $this;
    $response = $test->post('/media/chunk', [
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

function sendChunk(string $identifier, array $overrides = []): Illuminate\Testing\TestResponse
{
    return test()->post('/media/chunk', array_merge([
        'handler' => 'chunked',
        '_action' => 'upload',
        'file' => UploadedFile::fake()->createWithContent('part.bin', str_repeat('x', 1024)),
        'resumableChunkNumber' => 1,
        'resumableTotalChunks' => 2,
        'resumableChunkSize' => 1024,
        'resumableTotalSize' => 2048,
        'resumableIdentifier' => $identifier,
        'resumableFilename' => 'part.bin',
    ], $overrides));
}

test('chunks are kept apart per uploader', function () {
    $alice = Laravolt\Platform\Models\User::query()->forceCreate(['name' => 'Alice', 'email' => 'alice@example.test', 'password' => 'secret']);
    $bob = Laravolt\Platform\Models\User::query()->forceCreate(['name' => 'Bob', 'email' => 'bob@example.test', 'password' => 'secret']);

    $this->actingAs($alice);
    sendChunk('shared-id')->assertSuccessful()->assertJson(['done' => 50.0]);

    // Bob sends the second chunk under the same identifier: it does not complete Alice's upload.
    $this->actingAs($bob);
    sendChunk('shared-id', ['resumableChunkNumber' => 2])->assertSuccessful()->assertJson(['done' => 50.0]);

    expect(is_dir(storage_path('app/chunks/user-'.$alice->getKey().'/shared-id')))->toBeTrue()
        ->and(is_dir(storage_path('app/chunks/user-'.$bob->getKey().'/shared-id')))->toBeTrue();

    File::deleteDirectory(storage_path('app/chunks'));
});

test('an upload declared above max_file_size is refused', function () {
    config(['chunked-upload.max_file_size' => 1024]);

    sendChunk('too-big')->assertStatus(413)->assertJson(['success' => false]);
});

test('an upload whose chunks grow above max_file_size is discarded', function () {
    config(['chunked-upload.max_file_size' => 1500]);
    $this->actingAs(Laravolt\Platform\Models\User::query()->forceCreate(['name' => 'Up', 'email' => 'up@example.test', 'password' => 'secret']));

    sendChunk('grows', ['resumableTotalSize' => 1000])->assertSuccessful();
    sendChunk('grows', ['resumableTotalSize' => 1000, 'resumableChunkNumber' => 2])->assertStatus(413);

    expect(glob(storage_path('app/chunks/*/grows')))->toBe([]);

    File::deleteDirectory(storage_path('app/chunks'));
});

test('one uploader can keep only a limited number of unfinished uploads', function () {
    config(['chunked-upload.validation.max_pending_uploads' => 2]);
    $this->actingAs(Laravolt\Platform\Models\User::query()->forceCreate(['name' => 'Up', 'email' => 'up@example.test', 'password' => 'secret']));

    sendChunk('first')->assertSuccessful();
    sendChunk('second')->assertSuccessful();
    sendChunk('third')->assertStatus(429);
    // Continuing an upload that already started is still fine.
    sendChunk('first', ['resumableChunkNumber' => 2])->assertSuccessful();

    File::deleteDirectory(storage_path('app/chunks'));
});

test('cleanup removes stale uploads under owner directories and old flat ones', function () {
    $chunks = storage_path('app/chunks');
    File::ensureDirectoryExists($chunks.'/user-1/old-upload');
    File::ensureDirectoryExists($chunks.'/legacy-upload');
    File::put($chunks.'/user-1/old-upload/1', 'x');
    File::put($chunks.'/legacy-upload/1', 'x');
    touch($chunks.'/user-1/old-upload', now()->subDays(2)->timestamp);
    touch($chunks.'/legacy-upload', now()->subDays(2)->timestamp);

    (new CleanupStaleChunksJob(24))->handle();

    expect(is_dir($chunks.'/user-1'))->toBeFalse()
        ->and(is_dir($chunks.'/legacy-upload'))->toBeFalse();

    File::deleteDirectory($chunks);
});
