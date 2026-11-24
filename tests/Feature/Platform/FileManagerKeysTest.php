<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Crypt;
use Laravolt\Platform\Models\User;

beforeEach(function (): void {
    if (! app()->bound('laravolt.file-manager')) {
        app()->register(Laravolt\FileManager\ServiceProvider::class);
        Illuminate\Support\Facades\Route::getRoutes()->refreshNameLookups();
    }

    $this->root = sys_get_temp_dir().'/laravolt-file-manager-'.uniqid();
    mkdir($this->root);
    file_put_contents($this->root.'/backup.sql', 'dump');
    config(['filesystems.disks.backups' => ['driver' => 'local', 'root' => $this->root]]);

    $this->owner = User::query()->forceCreate(['name' => 'Owner', 'email' => 'owner@example.test', 'password' => 'secret']);
    $this->other = User::query()->forceCreate(['name' => 'Other', 'email' => 'other@example.test', 'password' => 'secret']);
});

afterEach(function (): void {
    @unlink($this->root.'/backup.sql');
    @rmdir($this->root);
});

function fileKeyFor(User $user): string
{
    test()->actingAs($user);

    return app('laravolt.file-manager')->encode('backups::backup.sql');
}

it('lets the user who was shown the file download it', function (): void {
    $key = fileKeyFor($this->owner);

    $this->actingAs($this->owner)
        ->get(route('file-manager::file.download', ['key' => $key]))
        ->assertOk();
});

it('rejects a download key issued to another user', function (): void {
    $key = fileKeyFor($this->owner);

    $this->actingAs($this->other)
        ->get(route('file-manager::file.download', ['key' => $key]))
        ->assertNotFound();
});

it('rejects a delete key issued to another user', function (): void {
    $key = fileKeyFor($this->owner);

    $this->actingAs($this->other)
        ->withoutMiddleware(Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class)
        ->delete(route('file-manager::file.destroy', $key))
        ->assertNotFound();

    expect(file_exists($this->root.'/backup.sql'))->toBeTrue();
});

it('rejects keys in the old unbound format and garbage', function (string $key): void {
    $this->actingAs($this->owner)
        ->get(route('file-manager::file.download', ['key' => $key]))
        ->assertNotFound();
})->with([
    'old format' => fn () => Crypt::encryptString(bin2hex('backups::backup.sql')),
    'not encrypted' => 'garbage',
]);
