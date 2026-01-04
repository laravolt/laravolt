<?php

declare(strict_types=1);

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Laravolt\Media\Jobs\CleanupStaleChunksJob;

beforeEach(function () {
    Storage::fake('local');
});

test('cleanup job can be instantiated with default hours', function () {
    $job = new CleanupStaleChunksJob();
    expect($job)->toBeInstanceOf(CleanupStaleChunksJob::class);
});

test('cleanup job can be instantiated with custom hours', function () {
    $job = new CleanupStaleChunksJob(48);
    expect($job)->toBeInstanceOf(CleanupStaleChunksJob::class);
});

test('cleanup job handles non-existent chunks directory gracefully', function () {
    $job = new CleanupStaleChunksJob(24);

    // Should not throw any exceptions when chunks directory doesn't exist
    expect(fn () => $job->handle())->not->toThrow(Exception::class);
});

test('cleanup job creates proper log entries', function () {
    $job = new CleanupStaleChunksJob(1);

    // Mock log to capture log entries
    Log::spy();

    $job->handle();

    // Should log completion
    Log::shouldHaveReceived('info')
        ->with('Chunked upload cleanup completed', Mockery::type('array'))
        ->once();
});

test('cleanup job handles exceptions properly', function () {
    $job = new CleanupStaleChunksJob(24);

    // Mock File facade to throw exception
    File::shouldReceive('exists')
        ->andThrow(new Exception('Test exception'));

    Log::spy();

    expect(fn () => $job->handle())->toThrow(Exception::class);

    // Should log the error
    Log::shouldHaveReceived('error')
        ->with('Chunked upload cleanup failed', Mockery::type('array'))
        ->once();
});

test('cleanup job calculates directory size correctly', function () {
    $job = new CleanupStaleChunksJob(24);

    // Use reflection to test private method
    $reflection = new ReflectionClass($job);
    $method = $reflection->getMethod('getDirectorySize');
    $method->setAccessible(true);

    // Test with non-existent directory
    $size = $method->invoke($job, '/non/existent/path');
    expect($size)->toBe(0);
});

test('cleanup job processes storage chunks correctly', function () {
    $job = new CleanupStaleChunksJob(1); // 1 hour

    // Create fake storage structure
    Storage::disk('local')->makeDirectory('chunks/test-chunk-1');
    Storage::disk('local')->makeDirectory('chunks/test-chunk-2');
    Storage::disk('local')->put('chunks/test-chunk-1/chunk1.tmp', 'test data 1');
    Storage::disk('local')->put('chunks/test-chunk-2/chunk2.tmp', 'test data 2');

    Log::spy();

    $job->handle();

    // Should log completion if any directories were processed
    Log::shouldHaveReceived('info')
        ->with('Chunked upload cleanup completed', Mockery::type('array'))
        ->once();
});

test('cleanup job failed method logs properly', function () {
    $job = new CleanupStaleChunksJob(24);
    $exception = new Exception('Test failure');

    Log::spy();

    $job->failed($exception);

    Log::shouldHaveReceived('error')
        ->with('Chunked upload cleanup job failed', Mockery::type('array'))
        ->once();
});

test('cleanup job respects stale hours configuration', function () {
    $shortJob = new CleanupStaleChunksJob(1);  // 1 hour
    $longJob = new CleanupStaleChunksJob(48);  // 48 hours

    // Both should handle execution without errors
    expect(fn () => $shortJob->handle())->not->toThrow(Exception::class);
    expect(fn () => $longJob->handle())->not->toThrow(Exception::class);
});

test('cleanup job can be dispatched', function () {
    Queue::fake();

    CleanupStaleChunksJob::dispatch(24);

    Queue::assertPushed(CleanupStaleChunksJob::class);
});

test('cleanup job has proper queue configuration', function () {
    $job = new CleanupStaleChunksJob(24);

    expect($job->tries)->toBe(3);
    expect($job->timeout)->toBe(300);
});
