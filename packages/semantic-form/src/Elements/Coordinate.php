<?php

namespace Laravolt\SemanticForm\Elements;

use Laravolt\Asset\AssetFacade;

class Coordinate extends Text
{
    protected static $sharedApiKey;

    protected $apiKey;

    public function __construct($name)
    {
        parent::__construct($name);
        $this->apiKey = static::$sharedApiKey;
    }

    public static function setApiKey(string $apiKey)
    {
        static::$sharedApiKey = $apiKey;
    }

    public function apiKey(string $apiKey)
    {
        $this->apiKey = $apiKey;

        return $this;
    }

    protected function beforeRender()
    {
        AssetFacade::addJs('http://maps.google.com/maps/api/js?sensor=false&key='.$this->apiKey);

        return true;
    }
}
