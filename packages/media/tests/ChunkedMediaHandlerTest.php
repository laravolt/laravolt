<?php

declare(strict_types=1);

namespace Laravolt\Media\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravolt\Media\MediaHandler\ChunkedMediaHandler;
use Laravolt\Platform\Models\Guest;
use Laravolt\Platform\Models\User;
use Tests\TestCase;

class ChunkedMediaHandlerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
    }

    /** @test */
    public function it_can_handle_chunk_upload()
    {
        // Create a test user
        $user = User::factory()->create();

        // Create a test file
        $file = UploadedFile::fake()->create('test-file.txt', 1024);

        // Mock the request with chunk data
        $request = request();
        $request->merge([
            'handler' => 'chunked',
            '_action' => 'upload',
        ]);
        $request->files->set('file', $file);

        // Create handler instance
        $handler = new ChunkedMediaHandler($request);

        // Execute the handler
        $response = $handler();

        // Assert response
        $this->assertEquals(200, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertTrue($responseData['success']);
    }

    /** @test */
    public function it_can_handle_guest_upload()
    {
        // Create a guest user
        $guest = Guest::first();

        // Create a test file
        $file = UploadedFile::fake()->create('guest-file.txt', 1024);

        // Mock the request
        $request = request();
        $request->merge([
            'handler' => 'chunked',
            '_action' => 'upload',
        ]);
        $request->files->set('file', $file);

        // Create handler instance
        $handler = new ChunkedMediaHandler($request);

        // Execute the handler
        $response = $handler();

        // Assert response
        $this->assertEquals(200, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertTrue($responseData['success']);
    }

    /** @test */
    public function it_can_handle_complete_upload()
    {
        // Create a test user
        $user = User::factory()->create();

        // Create a temporary file
        $tempFile = tempnam(sys_get_temp_dir(), 'test');
        file_put_contents($tempFile, 'test content');

        // Mock the request for complete action
        $request = request();
        $request->merge([
            'handler' => 'chunked',
            '_action' => 'complete',
            'file_id' => 'test-file-id',
            'file_name' => 'test-file.txt',
        ]);

        // Mock file existence
        $chunkPath = storage_path('app/chunks/test-file-id/test-file.txt');
        $chunkDir = dirname($chunkPath);
        if (!is_dir($chunkDir)) {
            mkdir($chunkDir, 0755, true);
        }
        copy($tempFile, $chunkPath);

        // Create handler instance
        $handler = new ChunkedMediaHandler($request);

        // Execute the handler
        $response = $handler();

        // Assert response
        $this->assertEquals(200, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertTrue($responseData['success']);
        $this->assertArrayHasKey('files', $responseData);
        $this->assertCount(1, $responseData['files']);

        // Clean up
        unlink($tempFile);
        if (file_exists($chunkPath)) {
            unlink($chunkPath);
            rmdir($chunkDir);
        }
    }

    /** @test */
    public function it_can_handle_delete_media()
    {
        // Create a test user
        $user = User::factory()->create();

        // Create a media item
        $media = $user->addMedia(UploadedFile::fake()->create('test-file.txt', 1024))
            ->toMediaCollection();

        // Mock the request for delete action
        $request = request();
        $request->merge([
            'handler' => 'chunked',
            '_action' => 'delete',
            'id' => $media->id,
        ]);

        // Create handler instance
        $handler = new ChunkedMediaHandler($request);

        // Execute the handler
        $response = $handler();

        // Assert response
        $this->assertEquals(200, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertTrue($responseData['success']);

        // Assert media is deleted
        $this->assertDatabaseMissing('media', ['id' => $media->id]);
    }

    /** @test */
    public function it_handles_upload_errors_gracefully()
    {
        // Mock the request without file
        $request = request();
        $request->merge([
            'handler' => 'chunked',
            '_action' => 'upload',
        ]);

        // Create handler instance
        $handler = new ChunkedMediaHandler($request);

        // Execute the handler
        $response = $handler();

        // Assert error response
        $this->assertEquals(400, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertFalse($responseData['success']);
        $this->assertArrayHasKey('message', $responseData);
    }

    /** @test */
    public function it_handles_complete_upload_without_file()
    {
        // Mock the request for complete action without file
        $request = request();
        $request->merge([
            'handler' => 'chunked',
            '_action' => 'complete',
        ]);

        // Create handler instance
        $handler = new ChunkedMediaHandler($request);

        // Execute the handler
        $response = $handler();

        // Assert error response
        $this->assertEquals(400, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertFalse($responseData['success']);
        $this->assertStringContains('Missing file_id or file_name', $responseData['message']);
    }

    /** @test */
    public function it_handles_complete_upload_with_nonexistent_file()
    {
        // Mock the request for complete action with nonexistent file
        $request = request();
        $request->merge([
            'handler' => 'chunked',
            '_action' => 'complete',
            'file_id' => 'nonexistent-file-id',
            'file_name' => 'nonexistent-file.txt',
        ]);

        // Create handler instance
        $handler = new ChunkedMediaHandler($request);

        // Execute the handler
        $response = $handler();

        // Assert error response
        $this->assertEquals(404, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertFalse($responseData['success']);
        $this->assertStringContains('File not found', $responseData['message']);
    }

    /** @test */
    public function it_returns_consistent_response_format()
    {
        // Create a test user
        $user = User::factory()->create();

        // Create a test file
        $file = UploadedFile::fake()->create('test-file.txt', 1024);

        // Mock the request
        $request = request();
        $request->merge([
            'handler' => 'chunked',
            '_action' => 'upload',
        ]);
        $request->files->set('file', $file);

        // Create handler instance
        $handler = new ChunkedMediaHandler($request);

        // Execute the handler
        $response = $handler();

        // Assert response format
        $responseData = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('success', $responseData);
        
        if ($responseData['success']) {
            $this->assertArrayHasKey('files', $responseData);
            $this->assertIsArray($responseData['files']);
            
            if (!empty($responseData['files'])) {
                $fileData = $responseData['files'][0];
                $this->assertArrayHasKey('file', $fileData);
                $this->assertArrayHasKey('name', $fileData);
                $this->assertArrayHasKey('size', $fileData);
                $this->assertArrayHasKey('type', $fileData);
                $this->assertArrayHasKey('data', $fileData);
                
                $this->assertArrayHasKey('id', $fileData['data']);
                $this->assertArrayHasKey('url', $fileData['data']);
                $this->assertArrayHasKey('thumbnail', $fileData['data']);
            }
        } else {
            $this->assertArrayHasKey('message', $responseData);
        }
    }
}