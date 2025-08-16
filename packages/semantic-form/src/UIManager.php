<?php

namespace Laravolt\SemanticForm;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Arr;
use InvalidArgumentException;

/**
 * UI Manager for handling multiple UI frameworks
 * 
 * This class manages switching between Semantic UI and Preline UI,
 * handles component mapping, and provides a unified interface for
 * UI framework configuration.
 */
class UIManager
{
    /**
     * The application instance.
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;

    /**
     * Currently active UI framework.
     *
     * @var string
     */
    protected $currentFramework;

    /**
     * Compiled UI configuration cache.
     *
     * @var array
     */
    protected static $compiledUIConfig = null;

    /**
     * CSS class mapping cache.
     *
     * @var array
     */
    protected static $classMappingCache = [];

    /**
     * Framework detection cache.
     *
     * @var array
     */
    protected static $detectionCache = [];

    /**
     * Create a new UI manager instance.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @return void
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->compileUIConfiguration();
        $this->currentFramework = $this->getDefaultFramework();
    }

    /**
     * Compile UI configuration for performance.
     *
     * @return void
     */
    protected function compileUIConfiguration()
    {
        if (static::$compiledUIConfig !== null) {
            return;
        }

        static::$compiledUIConfig = [
            'framework' => $this->app['config']->get('ui.framework', 'semantic'),
            'frameworks' => $this->app['config']->get('ui.frameworks', []),
            'component_mapping' => $this->app['config']->get('ui.component_mapping', []),
            'auto_detect' => $this->app['config']->get('ui.auto_detect', ['enabled' => false]),
            'performance' => $this->app['config']->get('ui.performance', []),
            'development' => $this->app['config']->get('ui.development', []),
        ];
    }

    /**
     * Get the default UI framework.
     *
     * @return string
     */
    public function getDefaultFramework()
    {
        return static::$compiledUIConfig['framework'] ?? 'semantic';
    }

    /**
     * Get the current UI framework.
     *
     * @return string
     */
    public function getCurrentFramework()
    {
        return $this->currentFramework ?: $this->getDefaultFramework();
    }

    /**
     * Switch to a specific UI framework.
     *
     * @param  string  $framework
     * @return $this
     */
    public function switchTo($framework)
    {
        if (!$this->isFrameworkAvailable($framework)) {
            throw new InvalidArgumentException("UI framework [{$framework}] is not available.");
        }

        if (!$this->isFrameworkEnabled($framework)) {
            throw new InvalidArgumentException("UI framework [{$framework}] is not enabled.");
        }

        $this->currentFramework = $framework;

        return $this;
    }

    /**
     * Check if a framework is available.
     *
     * @param  string  $framework
     * @return bool
     */
    public function isFrameworkAvailable($framework)
    {
        return isset(static::$compiledUIConfig['frameworks'][$framework]);
    }

    /**
     * Check if a framework is enabled.
     *
     * @param  string  $framework
     * @return bool
     */
    public function isFrameworkEnabled($framework)
    {
        $frameworkConfig = static::$compiledUIConfig['frameworks'][$framework] ?? [];
        return $frameworkConfig['enabled'] ?? false;
    }

    /**
     * Get available UI frameworks.
     *
     * @return array
     */
    public function getAvailableFrameworks()
    {
        return array_keys(static::$compiledUIConfig['frameworks'] ?? []);
    }

    /**
     * Get enabled UI frameworks.
     *
     * @return array
     */
    public function getEnabledFrameworks()
    {
        $enabled = [];
        
        foreach (static::$compiledUIConfig['frameworks'] ?? [] as $name => $config) {
            if ($config['enabled'] ?? false) {
                $enabled[] = $name;
            }
        }

        return $enabled;
    }

    /**
     * Get CSS class for a component in the current framework.
     *
     * @param  string  $component
     * @param  string|null  $framework
     * @return string
     */
    public function getCssClass($component, $framework = null)
    {
        $framework = $framework ?: $this->getCurrentFramework();
        
        $cacheKey = "{$framework}.{$component}";
        
        if (isset(static::$classMappingCache[$cacheKey])) {
            return static::$classMappingCache[$cacheKey];
        }

        // First check component mapping
        $mapping = static::$compiledUIConfig['component_mapping'][$component] ?? [];
        if (isset($mapping[$framework])) {
            static::$classMappingCache[$cacheKey] = $mapping[$framework];
            return $mapping[$framework];
        }

        // Fallback to framework-specific CSS classes
        $frameworkConfig = static::$compiledUIConfig['frameworks'][$framework] ?? [];
        $cssClasses = $frameworkConfig['settings']['css_classes'] ?? [];
        
        if (isset($cssClasses[$component])) {
            static::$classMappingCache[$cacheKey] = $cssClasses[$component];
            return $cssClasses[$component];
        }

        // Return empty string if no mapping found
        static::$classMappingCache[$cacheKey] = '';
        return '';
    }

    /**
     * Get framework configuration.
     *
     * @param  string|null  $framework
     * @return array
     */
    public function getFrameworkConfig($framework = null)
    {
        $framework = $framework ?: $this->getCurrentFramework();
        
        return static::$compiledUIConfig['frameworks'][$framework] ?? [];
    }

    /**
     * Get framework setting.
     *
     * @param  string  $setting
     * @param  string|null  $framework
     * @param  mixed  $default
     * @return mixed
     */
    public function getFrameworkSetting($setting, $framework = null, $default = null)
    {
        $framework = $framework ?: $this->getCurrentFramework();
        $config = $this->getFrameworkConfig($framework);
        
        return Arr::get($config, "settings.{$setting}", $default);
    }

    /**
     * Auto-detect the best UI framework.
     *
     * @return string
     */
    public function autoDetect()
    {
        $autoDetectConfig = static::$compiledUIConfig['auto_detect'] ?? [];
        
        if (!($autoDetectConfig['enabled'] ?? false)) {
            return $this->getDefaultFramework();
        }

        $cacheKey = 'auto_detect_result';
        
        if (isset(static::$detectionCache[$cacheKey])) {
            return static::$detectionCache[$cacheKey];
        }

        $priority = $autoDetectConfig['priority'] ?? ['preline', 'semantic'];
        $methods = $autoDetectConfig['detection_methods'] ?? [];

        foreach ($priority as $framework) {
            if (!$this->isFrameworkAvailable($framework)) {
                continue;
            }

            if ($this->detectFramework($framework, $methods)) {
                static::$detectionCache[$cacheKey] = $framework;
                return $framework;
            }
        }

        $default = $this->getDefaultFramework();
        static::$detectionCache[$cacheKey] = $default;
        return $default;
    }

    /**
     * Detect if a specific framework is configured.
     *
     * @param  string  $framework
     * @param  array  $methods
     * @return bool
     */
    protected function detectFramework($framework, $methods = [])
    {
        $frameworkConfig = $this->getFrameworkConfig($framework);
        $cssFramework = $frameworkConfig['css_framework'] ?? '';

        // Package.json detection
        if ($methods['package_json'] ?? true) {
            if ($this->detectInPackageJson($cssFramework)) {
                return true;
            }
        }

        // CSS file detection
        if ($methods['css_files'] ?? true) {
            if ($this->detectInCssFiles($cssFramework)) {
                return true;
            }
        }

        // Config hints detection
        if ($methods['config_hints'] ?? true) {
            if ($this->detectInConfig($framework)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Detect framework in package.json.
     *
     * @param  string  $cssFramework
     * @return bool
     */
    protected function detectInPackageJson($cssFramework)
    {
        static $packageJsonCache = null;
        
        if ($packageJsonCache === null) {
            $packageJsonPath = base_path('package.json');
            if (!file_exists($packageJsonPath)) {
                $packageJsonCache = false;
                return false;
            }

            $content = file_get_contents($packageJsonPath);
            $packageJsonCache = json_decode($content, true) ?: false;
        $cacheKey = 'ui_manager.package_json';
        $packageJsonCache = Cache::remember($cacheKey, 300, function () {
            $packageJsonPath = base_path('package.json');
            if (!file_exists($packageJsonPath)) {
                return false;
            }

            $content = file_get_contents($packageJsonPath);
            return json_decode($content, true) ?: false;
        });

        if (!$packageJsonCache) {
            return false;
        }

        $dependencies = array_merge(
            $packageJsonCache['dependencies'] ?? [],
            $packageJsonCache['devDependencies'] ?? []
        );

        switch ($cssFramework) {
            case 'tailwindcss':
                return isset($dependencies['tailwindcss']) || isset($dependencies['preline']);
            case 'semantic-ui':
                return isset($dependencies['semantic-ui']) || isset($dependencies['fomantic-ui']);
            default:
                return isset($dependencies[$cssFramework]);
        }
    }

    /**
     * Detect framework in CSS files.
     *
     * @param  string  $cssFramework
     * @return bool
     */
    protected function detectInCssFiles($cssFramework)
    {
        $publicPath = public_path();
        
        switch ($cssFramework) {
            case 'tailwindcss':
                return file_exists($publicPath . '/css/app.css') && 
                       $this->fileContains($publicPath . '/css/app.css', 'tailwind');
            case 'semantic-ui':
                return file_exists($publicPath . '/css/semantic.css') ||
                       file_exists($publicPath . '/semantic/semantic.css');
            default:
                return false;
        }
    }

    /**
     * Detect framework in configuration.
     *
     * @param  string  $framework
     * @return bool
     */
    protected function detectInConfig($framework)
    {
        // Check if explicitly configured
        $configFramework = $this->app['config']->get('ui.framework');
        if ($configFramework === $framework) {
            return true;
        }

        // Check form builder configuration
        $formBuilder = $this->app['config']->get('form.default');
        $frameworkConfig = $this->getFrameworkConfig($framework);
        
        return ($frameworkConfig['form_builder'] ?? '') === $formBuilder;
    }

    /**
     * Check if file contains specific content.
     *
     * @param  string  $filePath
     * @param  string  $content
     * @return bool
     */
    protected function fileContains($filePath, $content)
    {
        if (!file_exists($filePath)) {
            return false;
        }

        $fileContent = file_get_contents($filePath);
        return strpos($fileContent, $content) !== false;
    }

    /**
     * Get framework information.
     *
     * @param  string|null  $framework
     * @return array
     */
    public function getFrameworkInfo($framework = null)
    {
        $framework = $framework ?: $this->getCurrentFramework();
        $config = $this->getFrameworkConfig($framework);

        return [
            'name' => $config['name'] ?? ucfirst($framework),
            'framework' => $framework,
            'css_framework' => $config['css_framework'] ?? '',
            'js_framework' => $config['js_framework'] ?? '',
            'form_builder' => $config['form_builder'] ?? '',
            'enabled' => $config['enabled'] ?? false,
            'is_current' => $framework === $this->getCurrentFramework(),
            'settings' => $config['settings'] ?? [],
        ];
    }

    /**
     * Generate CSS class string for component.
     *
     * @param  string  $component
     * @param  array  $additionalClasses
     * @param  string|null  $framework
     * @return string
     */
    public function buildCssClass($component, $additionalClasses = [], $framework = null)
    {
        $baseClass = $this->getCssClass($component, $framework);
        
        if (empty($additionalClasses)) {
            return $baseClass;
        }

        $classes = array_filter(array_merge([$baseClass], (array) $additionalClasses));
        
        return implode(' ', $classes);
    }

    /**
     * Check if current framework is Semantic UI.
     *
     * @return bool
     */
    public function isSemantic()
    {
        return $this->getCurrentFramework() === 'semantic';
    }

    /**
     * Check if current framework is Preline UI.
     *
     * @return bool
     */
    public function isPreline()
    {
        return $this->getCurrentFramework() === 'preline';
    }

    /**
     * Get corresponding form builder for current framework.
     *
     * @return string
     */
    public function getFormBuilder()
    {
        $config = $this->getFrameworkConfig();
        return $config['form_builder'] ?? 'semantic';
    }

    /**
     * Clear compiled caches.
     *
     * @return void
     */
    public static function clearCache()
    {
        static::$compiledUIConfig = null;
        static::$classMappingCache = [];
        static::$detectionCache = [];
    }

    /**
     * Get performance statistics.
     *
     * @return array
     */
    public function getPerformanceStats()
    {
        return [
            'current_framework' => $this->getCurrentFramework(),
            'cached_css_mappings' => count(static::$classMappingCache),
            'detection_cache_hits' => count(static::$detectionCache),
            'compiled_config_size' => static::$compiledUIConfig ? sizeof(static::$compiledUIConfig) : 0,
            'memory_usage' => memory_get_usage(true),
        ];
    }
}