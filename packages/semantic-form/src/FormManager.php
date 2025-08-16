<?php

namespace Laravolt\SemanticForm;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Arr;
use InvalidArgumentException;
use Laravolt\SemanticForm\Contracts\FormBuilderInterface;

class FormManager
{
    /**
     * The application instance.
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;

    /**
     * The array of resolved form builders.
     *
     * @var array
     */
    protected $builders = [];

    /**
     * The current form builder driver.
     *
     * @var string
     */
    protected $currentDriver;

    /**
     * Create a new form manager instance.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @return void
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Get the default form builder name.
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        return $this->app['config']['form.default'];
    }

    /**
     * Get a form builder instance.
     *
     * @param  string|null  $driver
     * @return mixed
     */
    public function driver($driver = null)
    {
        $driver = $driver ?: $this->getDefaultDriver();

        // If we haven't created this driver yet, we'll create it for the first time
        if (! isset($this->builders[$driver])) {
            $this->builders[$driver] = $this->createDriver($driver);
        }

        $this->currentDriver = $driver;

        return $this->builders[$driver];
    }

    /**
     * Create a new form builder instance.
     *
     * @param  string  $driver
     * @return mixed
     *
     * @throws \InvalidArgumentException
     */
    protected function createDriver($driver)
    {
        $config = $this->getConfig($driver);

        if (! $config) {
            throw new InvalidArgumentException("Form builder [{$driver}] is not defined.");
        }

        $builderClass = $config['class'];

        if (! class_exists($builderClass)) {
            throw new InvalidArgumentException("Form builder class [{$builderClass}] does not exist.");
        }

        // Create the form builder instance
        $builder = new $builderClass($this->app['config']->get('laravolt.ui', []));

        // Set up error store and old input provider
        if (method_exists($builder, 'setErrorStore')) {
            $errorStoreClass = $this->getErrorStoreClass($driver);
            $builder->setErrorStore(new $errorStoreClass($this->app['session.store']));
        }

        if (method_exists($builder, 'setOldInputProvider')) {
            $oldInputClass = $this->getOldInputClass($driver);
            $builder->setOldInputProvider(new $oldInputClass($this->app['session.store']));
        }

        return $builder;
    }

    /**
     * Get the configuration for a form builder.
     *
     * @param  string  $driver
     * @return array
     */
    protected function getConfig($driver)
    {
        return Arr::get($this->app['config'], "form.builders.{$driver}");
    }

    /**
     * Get the error store class for a form builder.
     *
     * @param  string  $driver
     * @return string
     */
    protected function getErrorStoreClass($driver)
    {
        $config = $this->getConfig($driver);
        $namespace = $this->getNamespaceFromClass($config['class']);
        
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
        $config = $this->getConfig($driver);
        $namespace = $this->getNamespaceFromClass($config['class']);
        
        return $namespace . '\\OldInput\\IlluminateOldInputProvider';
    }

    /**
     * Extract namespace from class name.
     *
     * @param  string  $className
     * @return string
     */
    protected function getNamespaceFromClass($className)
    {
        $parts = explode('\\', $className);
        array_pop(); // Remove class name
        
        return implode('\\', $parts);
    }

    /**
     * Get all available form builders.
     *
     * @return array
     */
    public function getAvailableDrivers()
    {
        return array_keys($this->app['config']['form.builders']);
    }

    /**
     * Get the current form builder driver.
     *
     * @return string
     */
    public function getCurrentDriver()
    {
        return $this->currentDriver ?: $this->getDefaultDriver();
    }

    /**
     * Switch to a specific form builder.
     *
     * @param  string  $driver
     * @return mixed
     */
    public function switchTo($driver)
    {
        if (! $this->app['config']['form.runtime_switching.enabled']) {
            throw new InvalidArgumentException('Runtime switching is disabled.');
        }

        return $this->driver($driver);
    }

    /**
     * Check if auto-detection is enabled.
     *
     * @return bool
     */
    public function isAutoDetectionEnabled()
    {
        return $this->app['config']['form.auto_detect.enabled'];
    }

    /**
     * Auto-detect the best form builder based on the application setup.
     *
     * @return string
     */
    public function autoDetect()
    {
        if (! $this->isAutoDetectionEnabled()) {
            return $this->getDefaultDriver();
        }

        $detectionMethod = $this->app['config']['form.auto_detect.detection_method'];

        switch ($detectionMethod) {
            case 'css_scan':
                return $this->detectByCssFramework();
            case 'config':
                return $this->detectByConfig();
            default:
                return $this->getDefaultDriver();
        }
    }

    /**
     * Detect form builder by CSS framework.
     *
     * @return string
     */
    protected function detectByCssFramework()
    {
        // Check if Tailwind CSS is configured
        if ($this->isTailwindConfigured()) {
            return 'preline';
        }

        // Check if Semantic UI is configured
        if ($this->isSemanticUIConfigured()) {
            return 'semantic';
        }

        return $this->getDefaultDriver();
    }

    /**
     * Detect form builder by application configuration.
     *
     * @return string
     */
    protected function detectByConfig()
    {
        // Check UI configuration for hints about the preferred framework
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
     * Check if Tailwind CSS is configured.
     *
     * @return bool
     */
    protected function isTailwindConfigured()
    {
        // Check if tailwind.config.js exists
        if (file_exists(base_path('tailwind.config.js'))) {
            return true;
        }

        // Check package.json for tailwindcss
        $packageJsonPath = base_path('package.json');
        if (file_exists($packageJsonPath)) {
            $packageJson = json_decode(file_get_contents($packageJsonPath), true);
            return isset($packageJson['devDependencies']['tailwindcss']) || 
                   isset($packageJson['dependencies']['tailwindcss']);
        }

        return false;
    }

    /**
     * Check if Semantic UI is configured.
     *
     * @return bool
     */
    protected function isSemanticUIConfigured()
    {
        // Check package.json for semantic-ui
        $packageJsonPath = base_path('package.json');
        if (file_exists($packageJsonPath)) {
            $packageJson = json_decode(file_get_contents($packageJsonPath), true);
            return isset($packageJson['devDependencies']['semantic-ui']) || 
                   isset($packageJson['dependencies']['semantic-ui']) ||
                   isset($packageJson['devDependencies']['fomantic-ui']) || 
                   isset($packageJson['dependencies']['fomantic-ui']);
        }

        return false;
    }

    /**
     * Get form builder information.
     *
     * @param  string|null  $driver
     * @return array
     */
    public function getBuilderInfo($driver = null)
    {
        $driver = $driver ?: $this->getCurrentDriver();
        $config = $this->getConfig($driver);

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