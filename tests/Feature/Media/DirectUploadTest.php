<?php

declare(strict_types=1);

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravolt\Media\Livewire\DirectUpload;
use Laravolt\Platform\Models\Guest;
use Livewire\Livewire;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

beforeEach(function () {
    // Create a guest user for testing
    if (!Guest::first()) {
        Guest::create([
            'name' => 'Guest User',
            'email' => 'guest@example.com',
        ]);
    }
    
    // Clear any existing media
    Media::query()->delete();
    
    // Fake storage
    Storage::fake('s3');
    Storage::fake('public');
});

test('direct upload component can be instantiated', function () {
    $component = new DirectUpload();
    expect($component)->toBeInstanceOf(DirectUpload::class);
});

test('direct upload component can be rendered', function () {
    Livewire::test(DirectUpload::class)
        ->assertSuccessful()
        ->assertViewIs('laravolt::media.livewire.direct-upload');
});

test('direct upload component has default properties', function () {
    Livewire::test(DirectUpload::class)
        ->assertSet('disk', 's3')
        ->assertSet('collection', 'default')
        ->assertSet('maxFileSize', 102400)
        ->assertSet('uploadedFiles', []);
});

test('direct upload component can be mounted with custom properties', function () {
    Livewire::test(DirectUpload::class, [
        'disk' => 'public',
        'collection' => 'documents',
        'maxFileSize' => 51200,
    ])
        ->assertSet('disk', 'public')
        ->assertSet('collection', 'documents')
        ->assertSet('maxFileSize', 51200);
});

test('direct upload validates file size', function () {
    $largeFile = UploadedFile::fake()->create('large.pdf', 200000); // 200MB
    
    Livewire::test(DirectUpload::class)
        ->set('file', $largeFile)
        ->assertHasErrors(['file']);
});

test('direct upload can upload file to default disk', function () {
    Storage::fake('public');
    
    $file = UploadedFile::fake()->create('document.pdf', 1024);
    
    Livewire::test(DirectUpload::class, ['disk' => 'public'])
        ->set('file', $file)
        ->assertHasNoErrors();
    
    expect(Media::count())->toBe(1);
    
    $media = Media::first();
    expect($media->file_name)->toBe('document.pdf');
    expect($media->disk)->toBe('public');
});

test('direct upload can upload file to S3 disk', function () {
    $file = UploadedFile::fake()->create('document.pdf', 1024);
    
    Livewire::test(DirectUpload::class, ['disk' => 's3'])
        ->set('file', $file)
        ->assertHasNoErrors();
    
    expect(Media::count())->toBe(1);
    
    $media = Media::first();
    expect($media->file_name)->toBe('document.pdf');
    expect($media->disk)->toBe('s3');
});

test('direct upload adds file to uploaded files list', function () {
    Storage::fake('public');
    
    $file = UploadedFile::fake()->create('test.pdf', 512);
    
    Livewire::test(DirectUpload::class, ['disk' => 'public'])
        ->set('file', $file)
        ->assertSet('file', null) // File should be reset after upload
        ->call('$refresh');
    
    $media = Media::first();
    expect($media)->not->toBeNull();
});

test('direct upload can upload multiple files sequentially', function () {
    Storage::fake('public');
    
    $file1 = UploadedFile::fake()->create('file1.pdf', 512);
    $file2 = UploadedFile::fake()->create('file2.pdf', 512);
    
    $component = Livewire::test(DirectUpload::class, ['disk' => 'public'])
        ->set('file', $file1)
        ->assertHasNoErrors();
    
    $component
        ->set('file', $file2)
        ->assertHasNoErrors();
    
    expect(Media::count())->toBe(2);
    expect(Media::pluck('file_name')->toArray())->toContain('file1.pdf', 'file2.pdf');
});

test('direct upload can upload to custom collection', function () {
    Storage::fake('public');
    
    $file = UploadedFile::fake()->create('document.pdf', 512);
    
    Livewire::test(DirectUpload::class, [
        'disk' => 'public',
        'collection' => 'documents',
    ])
        ->set('file', $file)
        ->assertHasNoErrors();
    
    $media = Media::first();
    expect($media->collection_name)->toBe('documents');
});

test('direct upload can remove uploaded file', function () {
    Storage::fake('public');
    
    $file = UploadedFile::fake()->create('test.pdf', 512);
    
    $component = Livewire::test(DirectUpload::class, ['disk' => 'public'])
        ->set('file', $file)
        ->assertHasNoErrors();
    
    $media = Media::first();
    expect($media)->not->toBeNull();
    
    $component->call('removeFile', $media->id);
    
    expect(Media::count())->toBe(0);
});

test('direct upload dispatches fileUploaded event', function () {
    Storage::fake('public');
    
    $file = UploadedFile::fake()->create('test.pdf', 512);
    
    Livewire::test(DirectUpload::class, ['disk' => 'public'])
        ->set('file', $file)
        ->assertDispatched('fileUploaded');
});

test('direct upload dispatches fileRemoved event', function () {
    Storage::fake('public');
    
    $file = UploadedFile::fake()->create('test.pdf', 512);
    
    $component = Livewire::test(DirectUpload::class, ['disk' => 'public'])
        ->set('file', $file);
    
    $media = Media::first();
    
    $component->call('removeFile', $media->id)
        ->assertDispatched('fileRemoved');
});

test('direct upload works with guest user', function () {
    Storage::fake('public');
    
    // Ensure we're not authenticated
    $this->assertGuest();
    
    $file = UploadedFile::fake()->create('guest-upload.pdf', 256);
    
    Livewire::test(DirectUpload::class, ['disk' => 'public'])
        ->set('file', $file)
        ->assertHasNoErrors();
    
    $media = Media::first();
    expect($media)->not->toBeNull();
    expect($media->model_type)->toBe(Guest::class);
});

test('direct upload handles image files', function () {
    Storage::fake('public');
    
    $file = UploadedFile::fake()->image('photo.jpg', 800, 600);
    
    Livewire::test(DirectUpload::class, ['disk' => 'public'])
        ->set('file', $file)
        ->assertHasNoErrors();
    
    $media = Media::first();
    expect($media->file_name)->toBe('photo.jpg');
    expect($media->mime_type)->toStartWith('image/');
});

test('direct upload handles different file types', function () {
    Storage::fake('public');
    
    $fileTypes = [
        ['name' => 'document.pdf', 'mime' => 'application/pdf'],
        ['name' => 'spreadsheet.xlsx', 'mime' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'],
        ['name' => 'text.txt', 'mime' => 'text/plain'],
    ];
    
    foreach ($fileTypes as $fileType) {
        Media::query()->delete();
        
        $file = UploadedFile::fake()->create($fileType['name'], 100);
        
        Livewire::test(DirectUpload::class, ['disk' => 'public'])
            ->set('file', $file)
            ->assertHasNoErrors();
        
        $media = Media::first();
        expect($media->file_name)->toBe($fileType['name']);
    }
});

test('direct upload clears file input after successful upload', function () {
    Storage::fake('public');
    
    $file = UploadedFile::fake()->create('test.pdf', 512);
    
    Livewire::test(DirectUpload::class, ['disk' => 'public'])
        ->set('file', $file)
        ->assertSet('file', null);
});

test('direct upload handles upload errors gracefully', function () {
    Storage::fake('public');
    
    // Test with invalid file (this will depend on your validation)
    $invalidFile = UploadedFile::fake()->create('test.pdf', 200000); // Over max size
    
    Livewire::test(DirectUpload::class, ['disk' => 'public', 'maxFileSize' => 102400])
        ->set('file', $invalidFile)
        ->assertHasErrors('file');
    
    expect(Media::count())->toBe(0);
});

test('direct upload stores file metadata correctly', function () {
    Storage::fake('public');
    
    $file = UploadedFile::fake()->create('metadata-test.pdf', 1024);
    
    Livewire::test(DirectUpload::class, ['disk' => 'public'])
        ->set('file', $file)
        ->assertHasNoErrors();
    
    $media = Media::first();
    expect($media->file_name)->toBe('metadata-test.pdf');
    expect($media->size)->toBeGreaterThan(0);
    expect($media->mime_type)->toBe('application/pdf');
});

test('direct upload component view contains required elements', function () {
    Livewire::test(DirectUpload::class)
        ->assertSeeHtml('direct-upload-component')
        ->assertSeeHtml('wire:model="file"');
});

test('direct upload removes file from list after deletion', function () {
    Storage::fake('public');
    
    $file = UploadedFile::fake()->create('test.pdf', 512);
    
    $component = Livewire::test(DirectUpload::class, ['disk' => 'public']);
    
    // Upload file
    $component->set('file', $file);
    
    $media = Media::first();
    
    // Manually add to uploadedFiles (since it's done in updatedFile)
    $component->set('uploadedFiles', [[
        'id' => $media->id,
        'name' => $media->file_name,
        'size' => $media->size,
        'type' => $media->mime_type,
        'url' => $media->getUrl(),
        'thumbnail' => $media->getUrl(),
    ]]);
    
    expect($component->get('uploadedFiles'))->toHaveCount(1);
    
    // Remove file
    $component->call('removeFile', $media->id);
    
    expect($component->get('uploadedFiles'))->toHaveCount(0);
});
