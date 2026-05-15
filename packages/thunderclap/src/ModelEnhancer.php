<?php

declare(strict_types=1);

namespace Laravolt\Thunderclap;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ModelEnhancer
{
    protected $transformer;

    public function __construct($transformer = null)
    {
        $this->transformer = $transformer;
    }

    /**
     * Enhance existing model with required traits and properties
     */
    public function enhanceModel(array $modelInfo, array $enhancement, array $searchableColumns = [], ?string $factoryClass = null): bool
    {
        $modelPath = $modelInfo['path'];
        $content = File::get($modelPath);

        // Add missing traits
        if (! empty($enhancement['missing_traits'])) {
            $content = $this->addTraits($content, $enhancement['missing_traits']);
        }

        // Add or update searchableColumns
        if (! empty($searchableColumns) && ! $enhancement['has_searchable_columns']) {
            $content = $this->addSearchableColumns($content, $searchableColumns);
        }

        // Existing models need an explicit mass-assignment policy for generated CRUD.
        if (! $this->hasMassAssignmentProperty($content)) {
            $content = $this->addGuardedProperty($content);
        }

        if ($factoryClass !== null && ! $this->hasNewFactoryMethod($content)) {
            $content = $this->addFactoryMethod($content, $factoryClass);
        }

        return File::put($modelPath, $content) !== false;
    }

    /**
     * Create backup of model file
     */
    public function createBackup(string $modelPath): string
    {
        $backupPath = $modelPath.'.backup.'.date('YmdHis');
        File::copy($modelPath, $backupPath);

        return $backupPath;
    }

    /**
     * Remove backup of model file
     */
    public function removeBackup(string $backupPath): bool
    {
        if (File::exists($backupPath)) {
            return File::delete($backupPath);
        }

        return false;
    }

    /**
     * Restore model from backup
     */
    public function restoreFromBackup(string $modelPath, string $backupPath): bool
    {
        if (File::exists($backupPath)) {
            return File::move($backupPath, $modelPath);
        }

        return false;
    }

    /**
     * Add an import statement to the model if it does not already exist.
     */
    protected function addImport(string $content, string $class): string
    {
        $lines = explode("\n", $content);
        $useEndLine = null;
        $classLine = null;
        $useStatement = "use {$class};";

        foreach ($lines as $index => $line) {
            $trimmedLine = mb_trim($line);

            if ($trimmedLine === $useStatement) {
                return $content;
            }

            if (Str::startsWith($trimmedLine, 'use ') && Str::endsWith($trimmedLine, ';')) {
                $useEndLine = $index;
            }

            if (preg_match('/^(?:abstract\s+|final\s+|readonly\s+)*class\s+/', $trimmedLine) === 1) {
                $classLine = $index;
                break;
            }
        }

        $insertIndex = $useEndLine !== null ? $useEndLine + 1 : $classLine;
        if ($insertIndex === null) {
            return $content;
        }

        array_splice($lines, $insertIndex, 0, [$useStatement]);

        return implode("\n", $lines);
    }

    /**
     * Add traits to model
     */
    protected function addTraits(string $content, array $missingTraits): string
    {
        $content = $this->removeBareTraitImports($content, $missingTraits);

        foreach ($missingTraits as $trait) {
            $content = $this->addImport($content, $trait);
        }

        return $this->addTraitsToClass($content, $missingTraits);
    }

    /**
     * Remove malformed file-scope imports such as `use AutoFilter;`.
     */
    protected function removeBareTraitImports(string $content, array $traits): string
    {
        $shortTraitNames = array_map(fn (string $trait): string => class_basename($trait), $traits);
        $lines = explode("\n", $content);

        foreach ($lines as $index => $line) {
            $trimmedLine = mb_trim($line);

            if (preg_match('/^(?:abstract\s+|final\s+|readonly\s+)*class\s+/', $trimmedLine) === 1) {
                break;
            }

            foreach ($shortTraitNames as $traitName) {
                if ($trimmedLine === "use {$traitName};") {
                    unset($lines[$index]);
                    break;
                }
            }
        }

        return implode("\n", $lines);
    }

    /**
     * Add traits to class body
     */
    protected function addTraitsToClass(string $content, array $missingTraits): string
    {
        $traitNames = array_map(fn (string $trait): string => class_basename($trait), $missingTraits);

        // Find consecutive trait use statements at the start of the class body and collapse them.
        if (preg_match('/((?:abstract\s+|final\s+|readonly\s+)*class\s+\w+[^{}]*{\s*)((?:use\s+[^;]+;\s*)+)/s', $content, $matches)) {
            preg_match_all('/use\s+([^;]+);/', $matches[2], $traitMatches);
            $existingTraits = collect($traitMatches[1])
                ->flatMap(fn (string $traits) => array_map('mb_trim', explode(',', $traits)))
                ->map(fn (string $trait) => class_basename(ltrim($trait, '\\')))
                ->filter()
                ->all();

            $newTraits = array_values(array_unique(array_merge($existingTraits, $traitNames)));

            return preg_replace(
                '/((?:abstract\s+|final\s+|readonly\s+)*class\s+\w+[^{}]*{\s*)((?:use\s+[^;]+;\s*)+)/s',
                '$1use '.implode(', ', $newTraits).";\n\n",
                $content,
                1
            ) ?? $content;
        }

        // No existing trait use statement, add one after class opening brace.
        $useStatement = "\n    use ".implode(', ', $traitNames).";\n";

        return preg_replace('/(\n\s*(?:abstract\s+|final\s+|readonly\s+)*class\s+\w+[^{}]*{)(\s*)/', '$1'.$useStatement.'$2', $content, 1) ?? $content;
    }

    /**
     * Check if model already defines mass-assignment rules
     */
    protected function hasMassAssignmentProperty(string $content): bool
    {
        return preg_match('/\n\s*protected\s+\$(fillable|guarded)\s*=/', $content) === 1;
    }

    /**
     * Check if model already defines a factory resolver.
     */
    protected function hasNewFactoryMethod(string $content): bool
    {
        return preg_match('/\n\s*protected\s+static\s+function\s+newFactory\s*\(/', $content) === 1;
    }

    /**
     * Add module factory resolver to existing model.
     */
    protected function addFactoryMethod(string $content, string $factoryClass): string
    {
        $content = $this->addImport($content, $factoryClass);
        $factoryName = class_basename($factoryClass);
        $method = "\n    protected static function newFactory()\n    {\n        return {$factoryName}::new();\n    }\n";

        return preg_replace('/\n}/', $method."\n}", $content, 1) ?? $content;
    }

    /**
     * Add guarded property to model
     */
    protected function addGuardedProperty(string $content): string
    {
        return $this->insertProperty($content, "\n    protected \$guarded = [];\n");
    }

    /**
     * Add searchableColumns property to model
     */
    protected function addSearchableColumns(string $content, array $searchableColumns): string
    {
        $columnsString = "'".implode("', '", $searchableColumns)."'";
        $property = "\n    /** @var array<string> */\n    protected \$searchableColumns = [{$columnsString}];\n";

        return $this->insertProperty($content, $property);
    }

    /**
     * Insert property after existing properties or class traits
     */
    protected function insertProperty(string $content, string $property): string
    {
        // Find a good place to insert the property (after existing properties or use statements)
        if (preg_match('/(\n\s*protected\s+\$[^;]+;)/', $content)) {
            // Insert after last protected property
            return preg_replace('/(\n\s*protected\s+\$[^;]+;)(?!\n\s*protected\s+\$)/', '$1'.$property, $content, 1);
        }

        // Insert after use statements
        return preg_replace('/(\n\s*use\s+[^;]+;\s*)(\n\s*(?:public|protected|private|\}))/', '$1'.$property.'$2', $content);
    }
}
