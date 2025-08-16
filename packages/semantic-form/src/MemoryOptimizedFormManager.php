<?php

namespace Laravolt\SemanticForm;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Arr;
use InvalidArgumentException;

/**
 * Memory-Optimized Form Manager for High-Performance Applications
 * 
 * This version is optimized for:
 * - In-memory deployments
 * - Reduced object allocation
 * - Minimal file system access
 * - Static configuration caching
 * - Lazy loading of resources
 */
class MemoryOptimizedFormManager
{
    /**
     * The application instance.
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;

    /**
     * Pre-compiled configuration cache.
     *
     * @var array
     */
    protected static $compiledConfig = null;

    /**
     * Singleton form builder instances.
     *
     * @var array
     */
    protected static $instances = [];

    /**
     * Current driver cache.
     *
     * @var string
     */
    protected static $currentDriver = null;

    /**
     * Auto-detection result cache.
     *
     * @var string|null
     */
    protected static $autoDetectedDriver = null;

    /**
     * Configuration compilation flag.
     *
     * @var bool
     */
    protected static $isCompiled = false;

    /**
     * Create a new memory-optimized form manager instance.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @return void
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->compileConfiguration();
    }

    /**
     * Compile configuration for memory optimization.
     *
     * @return void
     */
    protected function compileConfiguration()
    {
        if (static::$isCompiled) {
            return;
        }

        // Cache the entire configuration in memory
        static::$compiledConfig = [
            'default' => $this->app['config']->get('form.default', 'semantic'),
            'builders' => $this->app['config']->get('form.builders', []),
            'auto_detect' => $this->app['config']->get('form.auto_detect', ['enabled' => false]),
            'runtime_switching' => $this->app['config']->get('form.runtime_switching', ['enabled' => true]),
            'compatibility_mode' => $this->app['config']->get('form.compatibility_mode', ['enabled' => true]),
        ];

        static::$isCompiled = true;
    }

    /**
     * Get the default form builder name.
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        return static::$compiledConfig['default'] ?? 'semantic';
    }

    /**
     * Get a form builder instance with memory optimization.
     *
     * @param  string|null  $driver
     * @return mixed
     */
    public function driver($driver = null)
    {
        $driver = $driver ?: $this->getDefaultDriver();

        // Use static instances for memory efficiency
        if (!isset(static::$instances[$driver])) {
            static::$instances[$driver] = $this->createOptimizedDriver($driver);
        }

        static::$currentDriver = $driver;

        return static::$instances[$driver];
    }

    /**
     * Create an optimized form builder instance.
     *
     * @param  string  $driver
     * @return mixed
     *
     * @throws \InvalidArgumentException
     */
    protected function createOptimizedDriver($driver)
    {
        $config = $this->getCachedConfig($driver);

        if (!$config) {
            throw new InvalidArgumentException("Form builder [{$driver}] is not defined.");
        }

        $builderClass = $config['class'];

        if (!class_exists($builderClass)) {
            throw new InvalidArgumentException("Form builder class [{$builderClass}] does not exist.");
        }

        // Create builder with minimal configuration
        $uiConfig = $this->app['config']->get('laravolt.ui', []);
        $builder = new $builderClass($uiConfig);

        // Set up dependencies with lazy loading
        $this->setupBuilderDependencies($builder, $driver);

        return $builder;
    }

    /**
     * Setup builder dependencies with lazy loading.
     *
     * @param  mixed  $builder
     * @param  string  $driver
     * @return void
     */
    protected function setupBuilderDependencies($builder, $driver)
    {
        if (method_exists($builder, 'setErrorStore')) {
            $errorStoreClass = $this->getErrorStoreClass($driver);
            $builder->setErrorStore(new $errorStoreClass($this->app['session.store']));
        }

        if (method_exists($builder, 'setOldInputProvider')) {
            $oldInputClass = $this->getOldInputClass($driver);
            $builder->setOldInputProvider(new $oldInputClass($this->app['session.store']));
        }
    }

    /**
     * Get cached configuration for a form builder.
     *
     * @param  string  $driver
     * @return array|null
     */
    protected function getCachedConfig($driver)
    {
        return static::$compiledConfig['builders'][$driver] ?? null;
    }

    /**
     * Get the error store class for a form builder.
     *
     * @param  string  $driver
     * @return string
     */
    protected function getErrorStoreClass($driver)
    {
        $config = $this->getCachedConfig($driver);
        $namespace = $this->extractNamespace($config['class']);
        
        return $namespace . '\\ErrorStore\\IlluminateErrorStore';
    }

    /**
     * Get the old input class for a form builder.
     *
     * @param  string  $driver
     * @return string
     */
    protected function getOldInputClass($driver)
    {
        $config = $this->getCachedConfig($driver);
        $namespace = $this->extractNamespace($config['class']);
        
        return $namespace . '\\OldInput\\IlluminateOldInputProvider';
    }

    /**
     * Extract namespace from class name (optimized version).
     *
     * @param  string  $className
     * @return string
     */
    protected function extractNamespace($className)
    {
        $lastBackslash = strrpos($className, '\\');
        return $lastBackslash !== false ? substr($className, 0, $lastBackslash) : '';
    }

    /**
     * Get all available form builders from cache.
     *
     * @return array
     */
    public function getAvailableDrivers()
    {
        return array_keys(static::$compiledConfig['builders'] ?? []);
    }

    /**
     * Get the current form builder driver.
     *
     * @return string
     */
    public function getCurrentDriver()
    {
        return static::$currentDriver ?: $this->getDefaultDriver();
    }

    /**
     * Switch to a specific form builder (memory-optimized).
     *
     * @param  string  $driver
     * @return mixed
     */
    public function switchTo($driver)
    {
        $runtimeConfig = static::$compiledConfig['runtime_switching'] ?? ['enabled' => true];
        
        if (!$runtimeConfig['enabled']) {
            throw new InvalidArgumentException('Runtime switching is disabled.');
        }

        return $this->driver($driver);
    }

    /**
     * Check if auto-detection is enabled (cached).
     *
     * @return bool
     */
    public function isAutoDetectionEnabled()
    {
        return static::$compiledConfig['auto_detect']['enabled'] ?? false;
    }

    /**
     * Auto-detect with caching for performance.
     *
     * @return string
     */
    public function autoDetect()
    {
        if (!$this->isAutoDetectionEnabled()) {
            return $this->getDefaultDriver();
        }

        // Use cached result if available
        if (static::$autoDetectedDriver !== null) {
            return static::$autoDetectedDriver;
        }

        $detectionMethod = static::$compiledConfig['auto_detect']['detection_method'] ?? 'css_scan';

        switch ($detectionMethod) {
            case 'css_scan':
                static::$autoDetectedDriver = $this->detectByCssFrameworkOptimized();
                break;
            case 'config':
                static::$autoDetectedDriver = $this->detectByConfigOptimized();
                break;
            default:
                static::$autoDetectedDriver = $this->getDefaultDriver();
        }

        return static::$autoDetectedDriver;
    }

    /**
     * Optimized CSS framework detection.
     *
     * @return string
     */
    protected function detectByCssFrameworkOptimized()
    {
        // Use cached detection results to avoid repeated file system access
        static $detectionCache = null;
        
        if ($detectionCache !== null) {
            return $detectionCache;
        }

        // Check for Tailwind first (more modern)
        if ($this->isTailwindConfiguredOptimized()) {
            $detectionCache = 'preline';
            return $detectionCache;
        }

        // Check for Semantic UI
        if ($this->isSemanticUIConfiguredOptimized()) {
            $detectionCache = 'semantic';
            return $detectionCache;
        }

        $detectionCache = $this->getDefaultDriver();
        return $detectionCache;
    }

    /**
     * Optimized configuration-based detection.
     *
     * @return string
     */
    protected function detectByConfigOptimized()
    {
        $uiConfig = $this->app['config']->get('laravolt.ui', []);
        
        if (isset($uiConfig['css_framework'])) {
            switch ($uiConfig['css_framework']) {
                case 'tailwind':
                case 'tailwindcss':
                    return 'preline';
                case 'semantic':
                case 'semantic-ui':
                    return 'semantic';
            }
        }

        return $this->getDefaultDriver();
    }

    /**
     * Optimized Tailwind CSS detection (memory-friendly).
     *
     * @return bool
     */
    protected function isTailwindConfiguredOptimized()
    {
        static $isTailwind = null;
        
        if ($isTailwind !== null) {
            return $isTailwind;
        }

        // Quick file existence check
        $tailwindConfigPath = base_path('tailwind.config.js');
        if (file_exists($tailwindConfigPath)) {
            $isTailwind = true;
            return $isTailwind;
        }

        // Optimized package.json check
        $isTailwind = $this->checkPackageJsonForDependency('tailwindcss');
        return $isTailwind;
    }

    /**
     * Optimized Semantic UI detection (memory-friendly).
     *
     * @return bool
     */
    protected function isSemanticUIConfiguredOptimized()
    {
        static $isSemantic = null;
        
        if ($isSemantic !== null) {
            return $isSemantic;
        }

        $isSemantic = $this->checkPackageJsonForDependency(['semantic-ui', 'fomantic-ui']);
        return $isSemantic;
    }

    /**
     * Optimized package.json dependency check.
     *
     * @param  string|array  $dependencies
     * @return bool
     */
    protected function checkPackageJsonForDependency($dependencies)
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
        }

        if (!$packageJsonCache) {
            return false;
        }

        $dependencies = (array) $dependencies;
        $allDependencies = array_merge(
            $packageJsonCache['dependencies'] ?? [],
            $packageJsonCache['devDependencies'] ?? []
        );

        foreach ($dependencies as $dependency) {
            if (isset($allDependencies[$dependency])) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get form builder information with caching.
     *
     * @param  string|null  $driver
     * @return array
     */
    public function getBuilderInfo($driver = null)
    {
        $driver = $driver ?: $this->getCurrentDriver();
        $config = $this->getCachedConfig($driver);

        if (!$config) {
            throw new InvalidArgumentException("Form builder [{$driver}] is not defined.");
        }

        return [
            'driver' => $driver,
            'class' => $config['class'],
            'ui_framework' => $config['ui_framework'],
            'css_framework' => $config['css_framework'],
            'description' => $config['description'],
            'is_current' => $driver === $this->getCurrentDriver(),
        ];
    }

    /**
     * Preload all form builders for high-performance scenarios.
     *
     * @return void
     */
    public function preloadAll()
    {
        $drivers = $this->getAvailableDrivers();
        
        foreach ($drivers as $driver) {
            if (!isset(static::$instances[$driver])) {
                static::$instances[$driver] = $this->createOptimizedDriver($driver);
            }
        }
    }

    /**
     * Clear memory caches (for testing or memory pressure).
     *
     * @return void
     */
    public static function clearMemoryCache()
    {
        static::$instances = [];
        static::$compiledConfig = null;
        static::$currentDriver = null;
        static::$autoDetectedDriver = null;
        static::$isCompiled = false;
    }

    /**
     * Get memory usage statistics.
     *
     * @return array
     */
    public function getMemoryStats()
    {
        return [
            'loaded_drivers' => array_keys(static::$instances),
            'compiled_config_size' => static::$compiledConfig ? sizeof(static::$compiledConfig) : 0,
            'memory_usage' => memory_get_usage(true),
            'peak_memory' => memory_get_peak_usage(true),
        ];
    }

    /**
     * Dynamically call the default driver instance.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return $this->driver()->$method(...$parameters);
    }
}