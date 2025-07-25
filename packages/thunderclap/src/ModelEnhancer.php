<?php

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
    public function enhanceModel(array $modelInfo, array $enhancement, array $searchableColumns = []): bool
    {
        $modelPath = $modelInfo['path'];
        $content = File::get($modelPath);

        // Add missing traits
        if (!empty($enhancement['missing_traits'])) {
            $content = $this->addTraits($content, $enhancement['missing_traits']);
        }

        // Add or update searchableColumns
        if (!empty($searchableColumns) && !$enhancement['has_searchable_columns']) {
            $content = $this->addSearchableColumns($content, $searchableColumns);
        }

        return File::put($modelPath, $content);
    }

    /**
     * Add traits to model
     */
    protected function addTraits(string $content, array $missingTraits): string
    {
        // Extract namespace and use statements
        $lines = explode("\n", $content);
        $useStatements = [];
        $classLine = null;
        $useEndLine = null;

        foreach ($lines as $index => $line) {
            $trimmedLine = trim($line);

            if (Str::startsWith($trimmedLine, 'use ') && Str::endsWith($trimmedLine, ';')) {
                $useStatements[] = $index;
                $useEndLine = $index;
            }

            if (Str::startsWith($trimmedLine, 'class ')) {
                $classLine = $index;
                break;
            }
        }

        // Add missing use statements
        $newUseStatements = [];
        foreach ($missingTraits as $trait) {
            $useStatement = "use {$trait};";

            // Check if use statement already exists
            $found = false;
            foreach ($lines as $line) {
                if (trim($line) === $useStatement) {
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                $newUseStatements[] = $useStatement;
            }
        }

        // Insert new use statements
        if (!empty($newUseStatements)) {
            $insertIndex = $useEndLine !== null ? $useEndLine + 1 : $classLine;
            array_splice($lines, $insertIndex, 0, $newUseStatements);
        }

        // Add traits to class
        $content = implode("\n", $lines);
        $content = $this->addTraitsToClass($content, $missingTraits);

        return $content;
    }

    /**
     * Add traits to class body
     */
    protected function addTraitsToClass(string $content, array $missingTraits): string
    {
        $traitNames = [];
        foreach ($missingTraits as $trait) {
            $traitNames[] = class_basename($trait);
        }

        // Find existing use statement in class
        if (preg_match('/class\s+\w+[^{]*{[^}]*?use\s+([^;]+);/s', $content, $matches)) {
            // Existing use statement found, append to it
            $existingTraits = $matches[1];
            $newTraits = $existingTraits . ', ' . implode(', ', $traitNames);
            $content = str_replace($matches[1], $newTraits, $content);
        } else {
            // No existing use statement, add one after class opening brace
            $useStatement = "\n    use " . implode(', ', $traitNames) . ";\n";
            $content = preg_replace('/(\n\s*class\s+\w+[^{]*{)(\s*)/', '$1' . $useStatement . '$2', $content);
        }

        return $content;
    }

    /**
     * Add searchableColumns property to model
     */
    protected function addSearchableColumns(string $content, array $searchableColumns): string
    {
        $columnsString = "'" . implode("', '", $searchableColumns) . "'";
        $property = "\n    /** @var array<string> */\n    protected \$searchableColumns = [{$columnsString}];\n";

        // Find a good place to insert the property (after existing properties or use statements)
        if (preg_match('/(\n\s*protected\s+\$[^;]+;)/', $content)) {
            // Insert after last protected property
            $content = preg_replace('/(\n\s*protected\s+\$[^;]+;)(?!\n\s*protected\s+\$)/', '$1' . $property, $content, 1);
        } else {
            // Insert after use statements
            $content = preg_replace('/(\n\s*use\s+[^;]+;\s*)(\n\s*(?:public|protected|private|\}))/', '$1' . $property . '$2', $content);
        }

        return $content;
    }

    /**
     * Create backup of model file
     */
    public function createBackup(string $modelPath): string
    {
        $backupPath = $modelPath . '.backup.' . date('YmdHis');
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
}
