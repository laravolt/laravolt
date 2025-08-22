<?php

namespace Laravolt\Workflow\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Laravolt\Workflow\Models\SharWorkflow;
use Laravolt\Workflow\SharWorkflowService;
use Laravolt\Workflow\Exceptions\SharException;

class SharWorkflowTable extends Component
{
    use WithPagination;

    public $search = '';
    public $status = '';
    public $showDeleteModal = false;
    public $workflowToDelete = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'status' => ['except' => ''],
    ];

    public function render()
    {
        $workflows = SharWorkflow::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->when($this->status, function ($query) {
                $query->where('status', $this->status);
            })
            ->with(['instances'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('workflow::livewire.shar-workflow-table', [
            'workflows' => $workflows,
        ]);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatus()
    {
        $this->resetPage();
    }

    public function confirmDelete($workflowName)
    {
        $this->workflowToDelete = $workflowName;
        $this->showDeleteModal = true;
    }

    public function deleteWorkflow()
    {
        if (!$this->workflowToDelete) {
            return;
        }

        try {
            $service = app(SharWorkflowService::class);
            $service->deleteWorkflow($this->workflowToDelete);

            session()->flash('message', 'Workflow deleted successfully.');
            $this->showDeleteModal = false;
            $this->workflowToDelete = null;
        } catch (SharException $e) {
            session()->flash('error', 'Failed to delete workflow: ' . $e->getMessage());
        }
    }

    public function cancelDelete()
    {
        $this->showDeleteModal = false;
        $this->workflowToDelete = null;
    }

    public function launchWorkflow($workflowName)
    {
        try {
            $service = app(SharWorkflowService::class);
            $instance = $service->launchWorkflowInstance($workflowName, [], auth()->id());

            session()->flash('message', "Workflow instance {$instance->id} launched successfully.");
        } catch (SharException $e) {
            session()->flash('error', 'Failed to launch workflow: ' . $e->getMessage());
        }
    }
}