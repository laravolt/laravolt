<?php

declare(strict_types=1);

namespace Laravolt\Tests\Feature\Ui;

use Illuminate\Support\Facades\Blade;
use Laravolt\Tests\FeatureTest;
use PHPUnit\Framework\Attributes\DataProvider;

class MediaPreviewTest extends FeatureTest
{
    /**
     * @return array<string, array{string, ?string, string, string}>
     */
    public static function files(): array
    {
        return [
            'image by extension' => ['/files/photo.JPG', null, 'image', '<img src="/files/photo.JPG"'],
            'pdf by mime' => ['/files/download?id=1', 'application/pdf', 'pdf', '<object data="/files/download?id=1" type="application/pdf"'],
            'video' => ['/files/clip.mp4', 'video/mp4', 'video', '<video src="/files/clip.mp4" controls'],
            'audio' => ['/files/voice.m4a', null, 'audio', '<audio src="/files/voice.m4a" controls'],
            'office file' => ['/files/deck.pptx', null, 'file', 'download'],
        ];
    }

    #[DataProvider('files')]
    public function test_it_picks_a_viewer_for_the_file_type(string $src, ?string $mime, string $kind, string $expected): void
    {
        $html = Blade::render('<x-volt-media-preview :src="$src" :mime="$mime" />', ['src' => $src, 'mime' => $mime]);

        $this->assertStringContainsString('data-media-preview="'.$kind.'"', $html);
        $this->assertStringContainsString($expected, $html);
    }

    public function test_the_url_is_escaped(): void
    {
        $html = Blade::render('<x-volt-media-preview :src="$src" />', ['src' => '/files/a.png" onerror="alert(1)']);

        $this->assertStringNotContainsString('onerror="alert(1)"', $html);
    }
}
