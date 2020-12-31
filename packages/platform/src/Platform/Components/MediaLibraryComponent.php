<?php

namespace Laravolt\Platform\Components;

use Illuminate\View\Component;
use Spatie\MediaLibrary\MediaCollections\MediaCollection;

class MediaLibraryComponent extends Component
{
    /** @var MediaCollection */
    public $collection;

    public $delete = false;

    protected $extensionColors = [
        'pdf' => 'red',
        'csv' => 'green',
        'xls' => 'green',
        'xlsx' => 'green',
        'doc' => 'blue',
        'docx' => 'blue',
        'odt' => 'blue',
        'jpg' => 'violet',
        'jpeg' => 'violet',
        'png' => 'violet',
        'gif' => 'violet',
    ];

    /**
     * MediaLibraryComponent constructor.
     */
    public function __construct($collection, bool $delete = false)
    {
        $this->collection = $collection;
        $this->delete = $delete;
    }

    public function convertExtensionToColor($extension)
    {
        return $this->extensionColors[$extension] ?? 'grey';
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('laravolt::components.media-library');
    }
}
