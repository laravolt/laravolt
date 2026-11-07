<?php

declare(strict_types=1);

namespace Laravolt\Tests\Unit\PrelineForm;

use Laravolt\PrelineForm\Elements\Filepond;
use Laravolt\PrelineForm\ErrorStore\IlluminateErrorStore;
use Laravolt\PrelineForm\PrelineForm;
use Laravolt\Tests\UnitTest;

class FilepondTest extends UnitTest
{
    public function test_it_waits_for_the_filepond_shipped_with_laravolt(): void
    {
        $html = (string) new Filepond('avatar');

        $this->assertStringContainsString('<input type="file"', $html);
        $this->assertStringContainsString('class="filepond"', $html);
        $this->assertStringContainsString('loader.ready()', $html);
        $this->assertLessThan(
            mb_strpos($html, 'FilePond.create('),
            mb_strpos($html, 'loader.ready()'),
        );
    }

    public function test_it_tolerates_an_uploader_script_without_ready(): void
    {
        $html = (string) new Filepond('avatar');

        $this->assertStringContainsString('loader && loader.ready ? loader.ready() : Promise.resolve()', $html);
    }

    public function test_without_a_server_the_file_is_posted_with_the_form(): void
    {
        $html = (string) new Filepond('avatar');

        $this->assertStringContainsString('FilePond.create(inputEl, {"storeAsFile":true})', $html);
    }

    public function test_with_a_server_the_file_is_uploaded_there(): void
    {
        $html = (string) (new Filepond('avatar'))->server('/upload')->instantUpload();

        $this->assertStringContainsString('"/upload"', $html);
        $this->assertStringContainsString('"instantUpload":true', $html);
        $this->assertStringNotContainsString('storeAsFile', $html);
    }

    public function test_a_server_on_this_site_gets_the_csrf_token(): void
    {
        app('session.store')->put('_token', 'pond-csrf-token');

        $relative = (string) (new Filepond('avatar'))->server('/upload');
        $absolute = (string) (new Filepond('avatar'))->server(url('/upload'));

        $this->assertStringContainsString(
            '"server":{"url":"/upload","headers":{"X-CSRF-TOKEN":"pond-csrf-token"}}',
            $relative,
        );
        $this->assertStringContainsString('"X-CSRF-TOKEN":"pond-csrf-token"', $absolute);
    }

    public function test_a_server_on_another_origin_never_gets_the_csrf_token(): void
    {
        app('session.store')->put('_token', 'pond-csrf-token');

        foreach (['https://uploads.example.net/process', '//uploads.example.net/process', '/\\uploads.example.net/process'] as $url) {
            $html = (string) (new Filepond('avatar'))->server($url);

            $this->assertStringNotContainsString('pond-csrf-token', $html, $url);
        }
    }

    public function test_the_form_builder_renders_a_labelled_field(): void
    {
        $form = new PrelineForm([]);
        $form->setErrorStore(new IlluminateErrorStore(app('session.store')));

        $html = (string) $form->filepond('pond')->label('FilePond Field');

        $this->assertStringContainsString('FilePond Field', $html);
        $this->assertStringContainsString('name="pond"', $html);
        $this->assertStringContainsString('FilePond.create(', $html);
    }

    public function test_multiple_files_are_posted_as_an_array(): void
    {
        $html = (string) (new Filepond('documents'))->multiple();

        $this->assertStringContainsString('name="documents[]"', $html);
        $this->assertStringContainsString('"allowMultiple":true', $html);
    }
}
