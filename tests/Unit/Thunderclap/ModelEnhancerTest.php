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
            $enhancer = new ModelEnhancer;

            $result = $enhancer->enhanceModel(
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
                ['sku', 'name']
            );

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
}
