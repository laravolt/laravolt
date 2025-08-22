@extends('laravolt::layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">SHAR Workflow Management</h1>
        <p class="text-gray-600 mt-2">Manage BPMN workflows using SHAR (Simple Hyperscale Activity Router)</p>
    </div>

    <!-- Health Status -->
    <div class="mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold mb-4">SHAR Server Status</h2>
            <div id="health-status" class="flex items-center">
                <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-blue-500 mr-2"></div>
                <span class="text-gray-600">Checking status...</span>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <a href="{{ route('workflow.shar.workflows.index') }}" class="bg-blue-500 hover:bg-blue-600 text-white rounded-lg p-6 transition-colors">
            <div class="flex items-center">
                <svg class="w-8 h-8 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <div>
                    <h3 class="text-lg font-semibold">Manage Workflows</h3>
                    <p class="text-blue-100">Create and manage BPMN workflows</p>
                </div>
            </div>
        </a>

        <a href="{{ route('workflow.shar.instances.index') }}" class="bg-green-500 hover:bg-green-600 text-white rounded-lg p-6 transition-colors">
            <div class="flex items-center">
                <svg class="w-8 h-8 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
                <div>
                    <h3 class="text-lg font-semibold">View Instances</h3>
                    <p class="text-green-100">Monitor workflow instances</p>
                </div>
            </div>
        </a>

        <a href="{{ route('workflow.shar.workflows.create') }}" class="bg-purple-500 hover:bg-purple-600 text-white rounded-lg p-6 transition-colors">
            <div class="flex items-center">
                <svg class="w-8 h-8 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                <div>
                    <h3 class="text-lg font-semibold">Create Workflow</h3>
                    <p class="text-purple-100">Upload new BPMN workflow</p>
                </div>
            </div>
        </a>
    </div>

    <!-- Statistics -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-xl font-semibold">Global Statistics</h2>
        </div>
        <div class="p-6">
            <div id="statistics-content" class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="text-center">
                    <div class="text-2xl font-bold text-blue-600" id="total-instances">-</div>
                    <div class="text-sm text-gray-600">Total Instances</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-green-600" id="running-instances">-</div>
                    <div class="text-sm text-gray-600">Running</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-gray-600" id="completed-instances">-</div>
                    <div class="text-sm text-gray-600">Completed</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-red-600" id="failed-instances">-</div>
                    <div class="text-sm text-gray-600">Failed</div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Check SHAR health status
    fetch('/api/shar/statistics')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('health-status').innerHTML = `
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-green-500 rounded-full mr-2"></div>
                        <span class="text-green-600 font-medium">SHAR Server Online</span>
                    </div>
                `;
                
                // Update statistics
                const stats = data.data;
                document.getElementById('total-instances').textContent = stats.total_instances || 0;
                document.getElementById('running-instances').textContent = stats.running_instances || 0;
                document.getElementById('completed-instances').textContent = stats.completed_instances || 0;
                document.getElementById('failed-instances').textContent = stats.failed_instances || 0;
            } else {
                document.getElementById('health-status').innerHTML = `
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-red-500 rounded-full mr-2"></div>
                        <span class="text-red-600 font-medium">SHAR Server Offline</span>
                    </div>
                `;
            }
        })
        .catch(error => {
            document.getElementById('health-status').innerHTML = `
                <div class="flex items-center">
                    <div class="w-3 h-3 bg-yellow-500 rounded-full mr-2"></div>
                    <span class="text-yellow-600 font-medium">Connection Error</span>
                </div>
            `;
        });
});
</script>
@endsection