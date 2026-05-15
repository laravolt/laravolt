<?php

declare(strict_types=1);

namespace Laravolt\Tests\Unit\Thunderclap;

use Doctrine\DBAL\Types\BooleanType;
use Doctrine\DBAL\Types\DecimalType;
use Doctrine\DBAL\Types\IntegerType;
use Doctrine\DBAL\Types\StringType;
use Illuminate\Support\Collection;
use Laravolt\Tests\UnitTest;
use Laravolt\Thunderclap\LaravoltTransformer;

class LaravoltTransformerTest extends UnitTest
{
    public function test_factory_attributes_match_database_column_types(): void
    {
        $transformer = new LaravoltTransformer;
        $transformer->setColumns(new Collection([
            'sku' => ['name' => 'sku', 'type' => new StringType, 'required' => true],
            'reorder_point' => ['name' => 'reorder_point', 'type' => new IntegerType, 'required' => true],
            'standard_cost' => ['name' => 'standard_cost', 'type' => new DecimalType, 'required' => true],
            'is_active' => ['name' => 'is_active', 'type' => new BooleanType, 'required' => true],
        ]));

        $attributes = $transformer->toTestFactoryAttributes();

        $this->assertStringContainsString("'sku' => \$this->faker->words(3, true),", $attributes);
        $this->assertStringContainsString("'reorder_point' => \$this->faker->numberBetween(1, 100),", $attributes);
        $this->assertStringContainsString("'standard_cost' => \$this->faker->randomFloat(2, 10, 1000),", $attributes);
        $this->assertStringContainsString("'is_active' => \$this->faker->boolean(),", $attributes);
    }

    public function test_update_attributes_match_database_column_types(): void
    {
        $transformer = new LaravoltTransformer;
        $transformer->setColumns(new Collection([
            'reorder_point' => ['name' => 'reorder_point', 'type' => new IntegerType, 'required' => true],
            'standard_cost' => ['name' => 'standard_cost', 'type' => new DecimalType, 'required' => true],
            'is_active' => ['name' => 'is_active', 'type' => new BooleanType, 'required' => true],
        ]));

        $attributes = $transformer->toTestUpdateAttributes();

        $this->assertStringContainsString("\$attributes['reorder_point'] = 123;", $attributes);
        $this->assertStringContainsString("\$attributes['standard_cost'] = 123.45;", $attributes);
    }
}
