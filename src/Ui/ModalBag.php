<?php

namespace Laravolt\Ui;

use Livewire\Component;

class ModalBag extends Component
{
    public ?string $activeModal;

    public array $modals = [];

    protected $listeners = ['openModal'];

    public function openModal($modal): void
    {
        $id = $modal.'-'.md5($modal);

        $this->modals[$id] = [
            'name' => $modal,
        ];

        $this->activeModal = $id;

        $this->emit('activeModalChanged', $id);
    }

    public function render()
    {
        return view('laravolt::ui-component.modal.modal-bag');
    }
}
