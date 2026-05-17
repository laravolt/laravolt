<?php

declare(strict_types=1);

namespace Laravolt\Tests\Unit\Ui;

use Laravolt\Tests\UnitTest;

/**
 * Regression for v7 P2-11: v7 Blade view files must not leak Fomantic /
 * Semantic UI class tokens. Only Preline + Tailwind utilities are allowed.
 *
 * Scope: views shipped under resources/views/components/ and
 * resources/views/ui-component/ — i.e. the v7 component layer applications
 * see. Legacy v6 packages (semantic-form, Suitable Dropdown column) are
 * not v7 deliverables and are excluded.
 */
class NoFomanticClassesTest extends UnitTest
{
    /** @var array<int, string> */
    private array $forbiddenTokens = [
        // Fomantic / Semantic UI components — must not appear as `class="ui <token>"`
        'ui button',
        'ui segment',
        'ui label',
        'ui menu',
        'ui form',
        'ui input',
        'ui grid',
        'ui header',
        'ui icon',
        'ui action',
        'ui primary',
        'ui message',
        'ui table',
        'ui card',
        'ui cards',
        'ui dropdown',
        'ui pointing',
        'ui attached',
        'ui inverted',
        'ui stackable',
        'ui basic',
        'ui fluid',
        'ui tab',
        'ui tabs',
        'ui column',
        'ui columns',
        'ui row',
    ];

    /** @var array<int, string> */
    private array $allowedDirectories = [];

    protected function setUp(): void
    {
        parent::setUp();

        $base = realpath(__DIR__.'/../../..');
        $this->allowedDirectories = [
            $base.'/resources/views/components',
            $base.'/resources/views/ui-component',
        ];
    }

    public function test_v7_view_files_contain_no_fomantic_class_tokens(): void
    {
        $violations = [];

        foreach ($this->allowedDirectories as $dir) {
            if (! is_dir($dir)) {
                continue;
            }

            $rii = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS),
            );

            foreach ($rii as $file) {
                /** @var \SplFileInfo $file */
                if (! $file->isFile() || ! str_ends_with($file->getFilename(), '.blade.php')) {
                    continue;
                }

                $contents = (string) file_get_contents($file->getPathname());

                foreach ($this->forbiddenTokens as $token) {
                    // Only match `class="..."` attribute values containing the token.
                    // Pattern: class attribute with the forbidden token as a whole-word
                    // sequence. We allow the token to appear in code comments / docblocks.
                    $pattern = '/class\s*=\s*"[^"]*\b'.preg_quote($token, '/').'\b[^"]*"/';
                    if (preg_match($pattern, $contents) === 1) {
                        $violations[] = $file->getPathname().' contains forbidden class token: "'.$token.'"';
                    }
                }
            }
        }

        $this->assertSame(
            [],
            $violations,
            'Forbidden Fomantic/Semantic UI class tokens found in v7 view files. Migrate to Preline + Tailwind utilities.'."\n".implode("\n", $violations),
        );
    }

    public function test_suitable_label_column_renders_preline_badge_markup(): void
    {
        $source = (string) file_get_contents(
            realpath(__DIR__.'/../../..').'/packages/suitable/src/Columns/Label.php',
        );

        $this->assertStringNotContainsString(
            'ui label',
            $source,
            'Suitable Label column must not render Fomantic `ui label` markup in v7.',
        );
        $this->assertStringContainsString(
            'rounded-full',
            $source,
            'Suitable Label column should render Preline pill/badge classes (e.g. rounded-full).',
        );
    }

    public function test_forbidden_token_list_is_non_empty_and_unique(): void
    {
        $this->assertNotEmpty($this->forbiddenTokens);
        $this->assertSame(
            $this->forbiddenTokens,
            array_values(array_unique($this->forbiddenTokens)),
            'Forbidden token list contains duplicates.',
        );
    }
}
