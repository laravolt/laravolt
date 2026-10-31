<?php

declare(strict_types=1);

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Laravolt\Platform\Models\User;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

beforeEach(function (): void {
    Storage::fake('public');
    $this->owner = User::query()->forceCreate(['name' => 'Owner', 'email' => 'owner@example.test', 'password' => 'secret']);
    $this->other = User::query()->forceCreate(['name' => 'Other', 'email' => 'other@example.test', 'password' => 'secret']);
    $this->media = $this->owner->addMedia(UploadedFile::fake()->create('report.pdf', 4))->toMediaCollection();
});

dataset('delete requests', [
    'chunked handler' => fn () => ['post', '/media/media', ['handler' => 'chunked', '_action' => 'delete']],
    'fileuploader handler' => fn () => ['post', '/media/media', ['handler' => 'fileuploader', '_action' => 'delete']],
]);

it('does not let a guest delete media', function (array $request): void {
    [$method, $url, $data] = $request;

    $this->withoutMiddleware(Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class)
        ->{$method.'Json'}($url, [...$data, 'id' => $this->media->getKey()])
        ->assertForbidden();

    expect(Media::query()->count())->toBe(1);
})->with('delete requests');

it('does not let a user delete media they do not own', function (array $request): void {
    [$method, $url, $data] = $request;

    $this->actingAs($this->other)
        ->withoutMiddleware(Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class)
        ->{$method.'Json'}($url, [...$data, 'id' => $this->media->getKey()])
        ->assertForbidden();

    expect(Media::query()->count())->toBe(1);
})->with('delete requests');

it('lets the owner delete their media', function (array $request): void {
    [$method, $url, $data] = $request;

    $this->actingAs($this->owner)
        ->withoutMiddleware(Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class)
        ->{$method.'Json'}($url, [...$data, 'id' => $this->media->getKey()])
        ->assertSuccessful();

    expect(Media::query()->count())->toBe(0);
})->with('delete requests');

it('protects the DELETE route the same way', function (): void {
    $this->withoutMiddleware(Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);

    $this->delete('/media/media/'.$this->media->getKey())->assertForbidden();
    $this->actingAs($this->other)->delete('/media/media/'.$this->media->getKey())->assertForbidden();
    expect(Media::query()->count())->toBe(1);

    $this->actingAs($this->owner)->delete('/media/media/'.$this->media->getKey())->assertRedirect();
    expect(Media::query()->count())->toBe(0);
});

it('lets the app allow more through a delete ability', function (): void {
    Gate::define('delete', fn (User $user, Media $media): bool => $user->is($this->other));

    $this->actingAs($this->other)
        ->withoutMiddleware(Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class)
        ->postJson('/media/media', ['handler' => 'chunked', '_action' => 'delete', 'id' => $this->media->getKey()])
        ->assertSuccessful();

    expect(Media::query()->count())->toBe(0);
});

it('rejects unknown actions', function (string $handler): void {
    $this->withoutMiddleware(Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class)
        ->postJson('/media/media', ['handler' => $handler, '_action' => 'saveFile'])
        ->assertStatus(400);
})->with(['chunked', 'fileuploader']);
