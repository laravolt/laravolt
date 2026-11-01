<?php

declare(strict_types=1);

namespace Laravolt\Tests\Unit\PrelineForm;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;
use Laravolt\PrelineForm\Elements\Uploader;
use Laravolt\PrelineForm\ErrorStore\IlluminateErrorStore;
use Laravolt\PrelineForm\OldInput\OldInputInterface;
use Laravolt\PrelineForm\PrelineForm;
use Laravolt\Tests\UnitTest;

class UploaderTest extends UnitTest
{
    protected function setUp(): void
    {
        parent::setUp();

        Route::post('media/media', fn () => null)->name('media::store');
        Route::getRoutes()->refreshNameLookups();
    }

    public function test_it_renders_a_field_the_uploader_script_picks_up(): void
    {
        $html = (string) (new Uploader('avatar'))->limit(3)->extensions(['jpg', 'png'])->fileMaxSize(5);

        $this->assertStringContainsString('type="file"', $html);
        $this->assertMatchesRegularExpression('/class="[^"]*\buploader\b[^"]*"/', $html);
        $this->assertStringContainsString('name="avatar"', $html);
        $this->assertStringContainsString('data-limit="3"', $html);
        $this->assertStringContainsString('data-extensions="jpg,png"', $html);
        $this->assertStringContainsString('data-file-max-size="5"', $html);
        $this->assertStringContainsString('data-media-url="'.url('media/media').'?handler=fileuploader"', $html);
    }

    public function test_without_ajax_the_files_are_posted_with_the_form(): void
    {
        $html = (string) (new Uploader('scans'))->ajax(false);

        $this->assertStringNotContainsString('data-media-url', $html);
    }

    public function test_a_custom_media_url_wins(): void
    {
        $html = (string) (new Uploader('avatar'))->mediaUrl('/upload');

        $this->assertStringContainsString('data-media-url="/upload"', $html);
    }

    public function test_existing_files_are_listed_for_the_script(): void
    {
        $uploader = (new Uploader('docs'))->value(json_encode([
            'https://cdn.example.test/storage/7/report%20final.pdf',
            '/storage/8/photo.PNG',
        ]));

        $files = $this->filesOf((string) $uploader);

        $this->assertSame('https://cdn.example.test/storage/7/report%20final.pdf', $files[0]['file']);
        $this->assertSame('report final.pdf', $files[0]['name']);
        $this->assertSame('application/pdf', $files[0]['type']);
        $this->assertSame(0, $files[0]['size']);
        $this->assertSame(url('storage/8/photo.PNG'), $files[1]['file']);
        $this->assertSame('image/png', $files[1]['type']);
        $this->assertStringNotContainsString('value=', (string) $uploader);
    }

    public function test_display_value_escapes_file_names(): void
    {
        $uploader = (new Uploader('docs'))->value('/storage/1/%3Cb%3Ex%3C%2Fb%3E.txt');

        $this->assertStringContainsString('&lt;b&gt;x&lt;/b&gt;.txt', $uploader->displayValue());
        $this->assertStringNotContainsString('<b>', $uploader->displayValue());
    }

    public function test_the_form_builder_wires_the_list_input_and_old_input(): void
    {
        session(['_token' => 'csrf-token']);
        $form = new PrelineForm([]);
        session(['errors' => (new ViewErrorBag)->put('default', new MessageBag(['_avatar' => ['Upload at least one photo.']]))]);
        $form->setErrorStore(new IlluminateErrorStore(app('session.store')));
        $form->setOldInputProvider(new class implements OldInputInterface
        {
            public function hasOldInput()
            {
                return true;
            }

            public function getOldInput($key)
            {
                return $key === '_avatar' ? '["https://example.test/storage/3/me.jpg"]' : null;
            }
        });

        $html = (string) $form->uploader('avatar');

        $this->assertStringContainsString('data-list-input="_avatar"', $html);
        $this->assertStringContainsString('data-token="csrf-token"', $html);
        $this->assertSame('https://example.test/storage/3/me.jpg', $this->filesOf($html)[0]['file']);
        $this->assertStringContainsString('border-red-500', $html);
        $this->assertStringContainsString('Upload at least one photo.', (string) $form->uploader('avatar')->label('Photo'));
    }

    /**
     * @return list<array{file: string, name: string, size: int, type: string}>
     */
    private function filesOf(string $html): array
    {
        $this->assertMatchesRegularExpression('/data-files="([^"]*)"/', $html);
        preg_match('/data-files="([^"]*)"/', $html, $match);

        return json_decode(html_entity_decode($match[1], ENT_QUOTES), true);
    }
}
