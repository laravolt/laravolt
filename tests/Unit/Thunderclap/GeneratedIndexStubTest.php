<?php

declare(strict_types=1);

namespace Laravolt\Tests\Unit\Thunderclap;

use Laravolt\Tests\UnitTest;

/**
 * Regression for v7 P0-1 / P0-2: the generated index Blade must keep its
 * blessed structure so the Preline TableView pattern stays the default.
 */
class GeneratedIndexStubTest extends UnitTest
{
    /** @var string */
    private string $indexStub;

    /** @var string */
    private string $tableViewStub;

    protected function setUp(): void
    {
        parent::setUp();

        $this->indexStub = (string) file_get_contents(
            __DIR__.'/../../../packages/thunderclap/stubs/laravolt/resources/views/index.blade.php.stub'
        );
        $this->tableViewStub = (string) file_get_contents(
            __DIR__.'/../../../packages/thunderclap/stubs/laravolt/TableView.php.stub'
        );
    }

    public function test_index_stub_renders_inside_volt_app_shell(): void
    {
        $this->assertStringContainsString('<x-volt-app', $this->indexStub);
        $this->assertStringContainsString('</x-volt-app>', $this->indexStub);
    }

    public function test_index_stub_uses_volt_link_button_for_add_action(): void
    {
        $this->assertStringContainsString('<x-volt-link-button', $this->indexStub);
        $this->assertStringContainsString('icon="plus"', $this->indexStub);
        $this->assertStringContainsString(
            "route('modules:::module-name:.create')",
            $this->indexStub,
            'Generated index must link to the module create route.'
        );
    }

    public function test_index_stub_registers_livewire_table_view(): void
    {
        $this->assertStringContainsString(
            '@livewire(\\:Namespace:\\:ModuleName:\\:ModuleName:TableView::class)',
            $this->indexStub,
            'Generated index must @livewire() the module TableView component.'
        );
    }

    public function test_index_stub_does_not_render_raw_table_markup(): void
    {
        $this->assertStringNotContainsString('<table', $this->indexStub);
        $this->assertStringNotContainsString('<thead', $this->indexStub);
        $this->assertStringNotContainsString('<tbody', $this->indexStub);
    }

    public function test_table_view_stub_uses_suitable_columns_and_inherits_core(): void
    {
        $this->assertStringContainsString('use Laravolt\\Ui\\TableView;', $this->tableViewStub);
        $this->assertStringContainsString(
            'extends TableView',
            $this->tableViewStub,
            'Generated TableView must extend the core Laravolt TableView.'
        );
        $this->assertStringContainsString('use Laravolt\\Suitable\\Columns\\Numbering;', $this->tableViewStub);
        $this->assertStringContainsString('use Laravolt\\Suitable\\Columns\\RestfulButton;', $this->tableViewStub);
        $this->assertStringContainsString('use Laravolt\\Suitable\\Columns\\Text;', $this->tableViewStub);
    }

    public function test_table_view_stub_wires_auto_sort_and_auto_search(): void
    {
        $this->assertStringContainsString('autoSort($this->sortPayload())', $this->tableViewStub);
        $this->assertStringContainsString('autoSearch(trim($this->search))', $this->tableViewStub);
    }
}
