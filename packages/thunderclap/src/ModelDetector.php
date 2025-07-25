<?php

namespace Laravolt\Thunderclap;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use ReflectionClass;

class ModelDetector
{
    protected $appModelsPath;
    protected $namespace;

    public function __construct()
    {
        $this->appModelsPath = app_path('Models');
        $this->namespace = 'App\\Models\\';
    }

    /**
     * Detect if a model exists for the given table name
     */
    public function detectExistingModel(string $table): ?array
    {
        $modelName = Str::singular(Str::studly($table));
        $modelPath = $this->appModelsPath . '/' . $modelName . '.php';
        $modelClass = $this->namespace . $modelName;

        if (File::exists($modelPath) && class_exists($modelClass) && $this->isEloquentModel($modelClass)) {
            // Verify the model actually uses the specified table
            $modelTable = $this->getTableFromModel($modelClass);
            if ($modelTable && $modelTable !== $table) {
                // Model exists but uses different table, check for naming variations
                $variations = $this->getTableNameVariations($table);
                if (!in_array($modelTable, $variations)) {
                    return null; // Model doesn't match the table
                }
            }

            return [
                'name' => $modelName,
                'path' => $modelPath,
                'class' => $modelClass,
                'namespace' => $this->namespace,
                'table' => $modelTable,
            ];
        }

        // Try alternative naming conventions
        $alternatives = $this->getModelNameAlternatives($table);
        foreach ($alternatives as $altName) {
            $altPath = $this->appModelsPath . '/' . $altName . '.php';
            $altClass = $this->namespace . $altName;

            if (File::exists($altPath) && class_exists($altClass) && $this->isEloquentModel($altClass)) {
                $modelTable = $this->getTableFromModel($altClass);
                if ($modelTable === $table) {
                    return [
                        'name' => $altName,
                        'path' => $altPath,
                        'class' => $altClass,
                        'namespace' => $this->namespace,
                        'table' => $modelTable,
                    ];
                }
            }
        }

        return null;
    }

    /**
     * Get all available models in app/Models directory
     */
    public function getAllModels(): array
    {
        if (!is_dir($this->appModelsPath)) {
            return [];
        }

        $models = [];
        $files = File::files($this->appModelsPath);

        foreach ($files as $file) {
            if ($file->getExtension() === 'php') {
                $modelName = $file->getFilenameWithoutExtension();
                $modelClass = $this->namespace . $modelName;

                if (class_exists($modelClass)) {
                    // Verify it's an Eloquent model
                    if ($this->isEloquentModel($modelClass)) {
                        $models[] = [
                            'name' => $modelName,
                            'path' => $file->getPathname(),
                            'class' => $modelClass,
                            'namespace' => $this->namespace,
                        ];
                    }
                }
            }
        }

        return $models;
    }

    /**
     * Check if model needs enhancement with traits
     */
    public function needsEnhancement(string $modelClass): array
    {
        $requiredTraits = [
            'Laravolt\Suitable\AutoFilter',
            'Laravolt\Suitable\AutoSearch',
            'Laravolt\Suitable\AutoSort',
        ];

        $missingTraits = [];

        if (class_exists($modelClass)) {
            $reflection = new ReflectionClass($modelClass);
            $usedTraits = $this->getAllTraits($reflection);

            foreach ($requiredTraits as $trait) {
                if (!in_array($trait, $usedTraits)) {
                    $missingTraits[] = $trait;
                }
            }
        }

        return [
            'needs_enhancement' => !empty($missingTraits),
            'missing_traits' => $missingTraits,
            'has_searchable_columns' => $this->hasSearchableColumns($modelClass),
        ];
    }

    /**
     * Get all traits used by a class including parent classes
     */
    protected function getAllTraits(ReflectionClass $reflection): array
    {
        $traits = [];

        while ($reflection) {
            $traits = array_merge($traits, array_keys($reflection->getTraits()));
            $reflection = $reflection->getParentClass();
        }

        return array_unique($traits);
    }

    /**
     * Check if model has searchableColumns property
     */
    protected function hasSearchableColumns(string $modelClass): bool
    {
        if (!class_exists($modelClass)) {
            return false;
        }

        $reflection = new ReflectionClass($modelClass);
        return $reflection->hasProperty('searchableColumns');
    }

    /**
     * Get table name from model
     */
    public function getTableFromModel(string $modelClass): ?string
    {
        if (!class_exists($modelClass)) {
            return null;
        }

        try {
            $model = new $modelClass;
            return $model->getTable();
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Suggest model name for table
     */
    public function suggestModelName(string $table): string
    {
        return Str::singular(Str::studly($table));
    }

    /**
     * Get table name variations (with and without prefixes)
     */
    protected function getTableNameVariations(string $table): array
    {
        $variations = [$table];

        // Add common prefix variations
        $prefixes = ['app_', 'tbl_', 'tb_'];
        foreach ($prefixes as $prefix) {
            if (Str::startsWith($table, $prefix)) {
                $variations[] = Str::after($table, $prefix);
            } else {
                $variations[] = $prefix . $table;
            }
        }

        return array_unique($variations);
    }

    /**
     * Get alternative model names for a table
     */
    protected function getModelNameAlternatives(string $table): array
    {
        $alternatives = [];

        // Standard naming
        $alternatives[] = Str::singular(Str::studly($table));

        // Without underscores
        $alternatives[] = Str::singular(Str::studly(str_replace('_', '', $table)));

        // Plural version (sometimes models are named plural)
        $alternatives[] = Str::studly($table);

        // With common prefixes/suffixes
        $base = Str::singular(Str::studly($table));
        $alternatives[] = $base . 'Model';
        $alternatives[] = 'App' . $base;

        return array_unique($alternatives);
    }

    /**
     * Check if a class is an Eloquent model
     */
    protected function isEloquentModel(string $class): bool
    {
        if (!class_exists($class)) {
            return false;
        }

        try {
            $reflection = new ReflectionClass($class);
            return $reflection->isSubclassOf('Illuminate\Database\Eloquent\Model');
        } catch (\Exception $e) {
            return false;
        }
    }
}
