<?php

namespace Laravolt\Ui;

use Livewire\Component;

class ModalBag extends Component
{
    public ?string $activeModal;

    public array $modals = [];

    protected $listeners = ['openModal'];

    public function openModal($modal, ?array $param = null): void
    {
        $this->activeModal = $modal.'-'.md5($modal);

        $this->modals[$this->activeModal] = [
            'name' => $modal,
            'param' => $param,
        ];

        $this->emit('activeModalChanged', $this->activeModal);
    }

    public function render()
    {
        return view('laravolt::ui-component.modal.modal-bag');
    }
}
