@extends('laravolt::layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">SHAR Workflows</h1>
                <p class="text-gray-600 mt-2">Manage your BPMN workflows</p>
            </div>
            <a href="{{ route('workflow.shar.index') }}" class="text-blue-600 hover:text-blue-800">
                ← Back to Dashboard
            </a>
        </div>
    </div>

    <livewire:laravolt::shar-workflow-table />
</div>
@endsection