<?php

declare(strict_types=1);

namespace Laravolt\Tests\Unit\Thunderclap;

use Laravolt\Suitable\AutoFilter;
use Laravolt\Suitable\AutoSearch;
use Laravolt\Suitable\AutoSort;
use Laravolt\Tests\UnitTest;
use Laravolt\Thunderclap\ModelEnhancer;

class ModelEnhancerTest extends UnitTest
{
    public function test_enhance_model_returns_boolean_after_writing_changes(): void
    {
        $modelPath = sys_get_temp_dir().'/laravolt-model-enhancer-'.uniqid().'.php';

        file_put_contents($modelPath, <<<'PHP'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
}
PHP);

        try {
            $result = $this->enhanceModel($modelPath, ['sku', 'name']);

            $content = file_get_contents($modelPath);

            $this->assertTrue($result);
            $this->assertStringContainsString('use Laravolt\\Suitable\\AutoFilter;', $content);
            $this->assertStringContainsString('use AutoFilter, AutoSearch, AutoSort;', $content);
            $this->assertStringContainsString('protected $guarded = [];', $content);
            $this->assertStringContainsString("protected \$searchableColumns = ['sku', 'name'];", $content);
        } finally {
            @unlink($modelPath);
        }
    }

    public function test_enhance_model_preserves_final_class_factory_import_when_adding_traits(): void
    {
        $modelPath = sys_get_temp_dir().'/laravolt-model-enhancer-'.uniqid().'.php';

        file_put_contents($modelPath, <<<'PHP'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class Item extends Model
{
    use HasFactory;
}
PHP);

        try {
            $result = $this->enhanceModel($modelPath, ['sku', 'name'], 'Modules\\Item\\Models\\ItemFactory');

            $content = file_get_contents($modelPath);

            $this->assertTrue($result);
            $this->assertStringContainsString('use Illuminate\\Database\\Eloquent\\Factories\\HasFactory;', $content);
            $this->assertStringNotContainsString('use Illuminate\\Database\\Eloquent\\Factories\\HasFactory,', $content);
            $this->assertStringContainsString('use Laravolt\\Suitable\\AutoFilter;', $content);
            $this->assertStringContainsString('use Laravolt\\Suitable\\AutoSearch;', $content);
            $this->assertStringContainsString('use Laravolt\\Suitable\\AutoSort;', $content);
            $this->assertStringContainsString('use Modules\\Item\\Models\\ItemFactory;', $content);
            $this->assertStringContainsString('    use HasFactory, AutoFilter, AutoSearch, AutoSort;', $content);
            $this->assertStringContainsString('        return ItemFactory::new();', $content);
        } finally {
            @unlink($modelPath);
        }
    }

    public function test_enhance_model_repairs_malformed_trait_imports(): void
    {
        $modelPath = sys_get_temp_dir().'/laravolt-model-enhancer-'.uniqid().'.php';

        file_put_contents($modelPath, <<<'PHP'
<?php

namespace App\Models;

use AutoFilter;
use AutoSearch;
use AutoSort;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class Item extends Model
{
    use HasFactory;
    use Laravolt\Suitable\AutoFilter;
    use AutoFilter, AutoSearch;
}
PHP);

        try {
            $result = $this->enhanceModel($modelPath, ['sku', 'name'], 'Modules\\Item\\Models\\ItemFactory');

            $content = file_get_contents($modelPath);

            $this->assertTrue($result);
            $this->assertStringNotContainsString("\nuse AutoFilter;", $content);
            $this->assertStringNotContainsString("\nuse AutoSearch;", $content);
            $this->assertStringNotContainsString("\nuse AutoSort;", $content);
            $this->assertStringContainsString('use Laravolt\\Suitable\\AutoFilter;', $content);
            $this->assertStringContainsString('use Laravolt\\Suitable\\AutoSearch;', $content);
            $this->assertStringContainsString('use Laravolt\\Suitable\\AutoSort;', $content);
            $this->assertStringContainsString('    use HasFactory, AutoFilter, AutoSearch, AutoSort;', $content);
            $this->assertSame(1, substr_count($content, '    use HasFactory, AutoFilter, AutoSearch, AutoSort;'));
        } finally {
            @unlink($modelPath);
        }
    }

    protected function enhanceModel(string $modelPath, array $searchableColumns, ?string $factoryClass = null): bool
    {
        $enhancer = new ModelEnhancer;

        return $enhancer->enhanceModel(
            [
                'path' => $modelPath,
                'class' => 'App\\Models\\Item',
            ],
            [
                'missing_traits' => [
                    AutoFilter::class,
                    AutoSearch::class,
                    AutoSort::class,
                ],
                'has_searchable_columns' => false,
            ],
            $searchableColumns,
            $factoryClass
        );
    }
}
