<?php

declare(strict_types=1);

namespace Laravolt\Platform\Components;

use Illuminate\View\Component;

class Modal extends Component
{
    public $key;

    public $size;

    public $wireModel;

    /**
     * ModalComponent constructor.
     */
    public function __construct(?string $key = null, string $size = 'md', ?string $wireModel = null)
    {
        $this->key = $key;
        $this->size = $size;
        $this->wireModel = $wireModel;
    }

    /**
     * Get the component key.
     */
    public function getKey(): string
    {
        return $this->key ?: 'modal-'.uniqid();
    }

    /**
     * Get the component size.
     */
    public function getSize(): string
    {
        return $this->size ?: 'md';
    }

    /**
     * Get the wire model.
     */
    public function getWireModel(): ?string
    {
        return $this->wireModel;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('laravolt::components.modal');
    }
}
