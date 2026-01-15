<?php

declare(strict_types=1);

namespace Laravolt\Ui;

use Livewire\Attributes\On;
use Livewire\Component;

class ModalBag extends Component
{
    public ?string $activeModal;

    public array $modals = [];

    #[On('openModal')]
    public function openModal($modal): void
    {
        $id = $modal.'-'.md5($modal);

        $this->modals[$id] = [
            'name' => $modal,
        ];

        $this->activeModal = $id;

        $this->dispatch('activeModalChanged', $id);
    }

    public function render()
    {
        return view('laravolt::ui-component.modal.modal-bag');
    }
}
