<?php

namespace Laravolt\Tests\Unit\PrelineForm;

use Laravolt\PrelineForm\ServiceProvider;
use Laravolt\Tests\UnitTest;

class FieldCollectionLayoutTest extends UnitTest
{
    protected function getPackageProviders($app)
    {
        return [
            ServiceProvider::class,
        ];
    }

    public function test_grid_layout_can_be_declared_from_field_collection_config()
    {
        $html = (string) form()->make([
            [
                'type' => 'grid',
                'columns' => 3,
                'gap' => 6,
                'class' => 'items-start',
                'items' => [
                    ['type' => 'text', 'name' => 'first_name'],
                    ['type' => 'email', 'name' => 'email', 'colSpan' => 2, 'colStart' => 2],
                    ['type' => 'textarea', 'name' => 'bio', 'columnSpan' => 'full', 'layoutClass' => 'md:col-start-1'],
                ],
            ],
        ]);

        $this->assertStringContainsString('grid grid-cols-3 gap-6 items-start', $html);
        $this->assertStringContainsString('class="col-span-1"', $html);
        $this->assertStringContainsString('class="col-span-2 col-start-2"', $html);
        $this->assertStringContainsString('class="col-span-full md:col-start-1"', $html);
        $this->assertStringContainsString('name="first_name"', $html);
        $this->assertStringContainsString('name="email"', $html);
        $this->assertStringContainsString('name="bio"', $html);
    }

    public function test_row_layout_supports_nested_items_and_width_alias()
    {
        $html = (string) form()->make([
            [
                'type' => 'row',
                'columns' => 4,
                'items' => [
                    ['type' => 'text', 'name' => 'title', 'width' => 3],
                    [
                        'type' => 'grid',
                        'columns' => 2,
                        'colSpan' => 1,
                        'items' => [
                            ['type' => 'text', 'name' => 'city'],
                            ['type' => 'text', 'name' => 'postal_code'],
                        ],
                    ],
                ],
            ],
        ]);

        $this->assertStringContainsString('grid grid-cols-4 gap-4', $html);
        $this->assertStringContainsString('class="col-span-3"', $html);
        $this->assertStringContainsString('grid grid-cols-2 gap-4', $html);
        $this->assertStringContainsString('name="city"', $html);
        $this->assertStringContainsString('name="postal_code"', $html);
    }

    public function test_bind_values_reaches_fields_inside_layouts()
    {
        $fields = form()->make([
            [
                'type' => 'grid',
                'columns' => 2,
                'items' => [
                    ['type' => 'text', 'name' => 'name'],
                    ['type' => 'email', 'name' => 'email'],
                ],
            ],
        ])->bindValues([
            'name' => 'Rama',
            'email' => 'rama@example.test',
        ]);

        $html = (string) $fields;

        $this->assertStringContainsString('value="Rama"', $html);
        $this->assertStringContainsString('value="rama@example.test"', $html);
    }
}
