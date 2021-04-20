<?php

namespace Laravolt\Asset;

use Laravolt\Asset\Exceptions\AssetCollectionNotFoundException;

class AssetManager
{
    /**
     * Regex to match against a filename/url to determine if it is an asset.
     *
     * @var string
     */
    protected $assetRegex = '/.\.(css|js)$/i';
    /**
     * Regex to match against a filename/url to determine if it is a CSS asset.
     *
     * @var string
     */
    protected $cssRegex = '/.\.css$/i';
    /**
     * Regex to match against a filename/url to determine if it is a JavaScript asset.
     *
     * @var string
     */
    protected $jsRegex = '/.\.js$/i';
    /**
     * Available collections.
     * Each collection is an array of assets.
     * Collections may also contain other collections.
     *
     * @var array
     */
    protected $collections = [];
    /**
     * CSS files already added.
     * Not accepted as an option of config() method.
     *
     * @var array
     */
    protected $css = [];
    /**
     * JavaScript files already added.
     * Not accepted as an option of config() method.
     *
     * @var array
     */
    protected $js = [];

    /**
     * Class constructor.
     *
     * @param array $options See config() method for details.
     *
     * @return void
     */
    public function __construct(array $options = [])
    {
        // Forward config options
        if ($options) {
            $this->config($options);
        }
    }

    /**
     * Set up configuration options.
     * All the class properties except 'js' and 'css' are accepted here.
     * Also, an extra option 'autoload' may be passed containing an array of
     * assets and/or collections that will be automatically added on startup.
     *
     * @param array $config Configurable options.
     *
     * @return self
     */
    public function config(array $config)
    {
        // Set regex options
        foreach (['asset_regex'] as $option) {
            if (isset($config[$option]) && (@preg_match($config[$option], null) !== false)) {
                $this->$option = $config[$option];
            }
        }

        // Set collections
        if (isset($config['collections']) && is_array($config['collections'])) {
            $this->collections = $config['collections'];
        }

        // Autoload assets
        if (isset($config['autoload']) && is_array($config['autoload'])) {
            foreach ($config['autoload'] as $asset) {
                $this->add($asset);
            }
        }

        return $this;
    }

    /**
     * Add an asset or a collection of assets.
     * It automatically detects the asset type (JavaScript, CSS or collection).
     * You may add more than one asset passing an array as argument.
     *
     * @param mixed $asset
     *
     * @return self
     */
    public function add(string | array $asset)
    {
        // More than one asset
        if (is_array($asset)) {
            foreach ($asset as $a) {
                $this->add($a);
            }
        } // Collection
        elseif (isset($this->collections[$asset])) {
            $this->add($this->collections[$asset]);
        } // JavaScript asset
        elseif (preg_match($this->jsRegex, $asset)) {
            $this->addJs($asset);
        } // CSS asset
        elseif (preg_match($this->cssRegex, $asset)) {
            $this->addCss($asset);
        } else {
            throw new AssetCollectionNotFoundException(sprintf('Collection "%s" not registered', $asset));
        }

        return $this;
    }

    /**
     * Add a CSS asset.
     * It checks for duplicates.
     * You may add more than one asset passing an array as argument.
     *
     * @param mixed $asset
     *
     * @return self
     */
    public function addCss($asset)
    {
        if (is_array($asset)) {
            foreach ($asset as $a) {
                $this->addCss($a);
            }

            return $this;
        }

        if (!$this->isRemoteLink($asset)) {
            $asset = $this->buildLocalLink($asset);
        }

        if (!in_array($asset, $this->css)) {
            $this->css[] = $asset;
        }

        return $this;
    }

    /**
     * Add a JavaScript asset.
     * It checks for duplicates.
     * You may add more than one asset passing an array as argument.
     *
     * @param mixed $asset
     *
     * @return self
     */
    public function addJs($asset)
    {
        if (is_array($asset)) {
            foreach ($asset as $a) {
                $this->addJs($a);
            }

            return $this;
        }

        if (!$this->isRemoteLink($asset)) {
            $asset = $this->buildLocalLink($asset);
        }

        if (!in_array($asset, $this->js)) {
            $this->js[] = $asset;
        }

        return $this;
    }

    /**
     * Build the CSS `<link>` tags.
     * Accepts an array of $attributes for the HTML tag.
     * You can take control of the tag rendering by
     * providing a closure that will receive an array of assets.
     *
     * @param array|Closure $attributes
     *
     * @return string
     */
    public function css($attributes = null)
    {
        if (!$this->css) {
            return '';
        }

        // Build attributes
        $attributes = (array) $attributes;
        unset($attributes['href']);

        if (!array_key_exists('type', $attributes)) {
            $attributes['type'] = 'text/css';
        }

        if (!array_key_exists('rel', $attributes)) {
            $attributes['rel'] = 'stylesheet';
        }

        $attributes = $this->buildTagAttributes($attributes);

        // Build tags
        $output = '';
        foreach ($this->css as $asset) {
            $output .= '<link href="'.$asset.'"'.$attributes." />\n";
        }

        return $output;
    }

    /**
     * Build the JavaScript `<script>` tags.
     * Accepts an array of $attributes for the HTML tag.
     * You can take control of the tag rendering by
     * providing a closure that will receive an array of assets.
     *
     * @param array|Closure $attributes
     *
     * @return string
     */
    public function js($attributes = null)
    {
        if (!$this->js) {
            return '';
        }

        // Build attributes
        $attributes = (array) $attributes;
        unset($attributes['src']);

        if (!array_key_exists('type', $attributes)) {
            $attributes['type'] = 'text/javascript';
        }

        $attributes = $this->buildTagAttributes($attributes);

        // Build tags
        $assets = $this->js;
        $output = '';
        foreach ($assets as $asset) {
            $output .= '<script src="'.$asset.'"'.$attributes."></script>\n";
        }

        return $output;
    }

    /**
     * Add/replace collection.
     *
     * @param string $collectionName
     * @param array  $assets
     *
     * @return self
     */
    public function registerCollection($collectionName, array $assets)
    {
        $this->collections[$collectionName] = $assets;

        return $this;
    }

    /**
     * Build link to local asset.
     * Detects packages links.
     *
     * @param string $asset
     *
     * @return string the link
     */
    protected function buildLocalLink($asset)
    {
        return asset($asset);
    }

    /**
     * Build an HTML attribute string from an array.
     *
     * @param array $attributes
     *
     * @return string
     */
    public function buildTagAttributes(array $attributes): string
    {
        $html = [];

        foreach ($attributes as $key => $value) {
            if (is_null($value)) {
                continue;
            }

            if (is_numeric($key)) {
                $key = $value;
            }

            $html[] = $key.'="'.htmlentities($value, ENT_QUOTES, 'UTF-8', false).'"';
        }

        return !empty($html) ? ' '.implode(' ', $html) : '';
    }

    /**
     * Determine whether a link is local or remote.
     * Undestands both "http://" and "https://" as well as protocol agnostic links "//".
     *
     * @param string $link
     *
     * @return bool
     */
    protected function isRemoteLink($link)
    {
        return
            str_starts_with($link, 'http://') || str_starts_with($link, 'https://') || str_starts_with($link, '//');
    }
}
