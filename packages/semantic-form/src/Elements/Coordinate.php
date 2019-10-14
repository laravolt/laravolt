<?php namespace Laravolt\SemanticForm\Elements;

use Illuminate\Database\Eloquent\Model;

class Coordinate extends Text
{
    protected $apiKey;

    public function apiKey(string $apiKey)
    {
        $this->apiKey = $apiKey;

        return $this;
    }

    protected function beforeRender()
    {
        \Stolz\Assets\Laravel\Facade::addJs('http://maps.google.com/maps/api/js?sensor=false&key='.$this->apiKey);

        return true;
    }
}
