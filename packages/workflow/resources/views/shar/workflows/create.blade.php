@extends('laravolt::layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Create BPMN Workflow</h1>
        <p class="text-gray-600 mt-2">Upload a new BPMN workflow to SHAR</p>
    </div>

    <div class="bg-white rounded-lg shadow">
        <form id="workflow-form" class="p-6 space-y-6">
            @csrf
            
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                    Workflow Name *
                </label>
                <input 
                    type="text" 
                    id="name" 
                    name="name" 
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Enter workflow name"
                >
                <p class="mt-1 text-sm text-gray-500">Unique identifier for the workflow</p>
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                    Description
                </label>
                <textarea 
                    id="description" 
                    name="description" 
                    rows="3"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Enter workflow description (optional)"
                ></textarea>
            </div>

            <div>
                <label for="bpmn_file" class="block text-sm font-medium text-gray-700 mb-2">
                    BPMN File *
                </label>
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-gray-400 transition-colors">
                    <input 
                        type="file" 
                        id="bpmn_file" 
                        name="bpmn_file" 
                        accept=".bpmn,.xml"
                        required
                        class="hidden"
                        onchange="handleFileSelect(this)"
                    >
                    <div id="file-drop-zone" onclick="document.getElementById('bpmn_file').click()">
                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <p class="mt-2 text-sm text-gray-600">
                            <span class="font-medium text-blue-600 hover:text-blue-500 cursor-pointer">Click to upload</span>
                            or drag and drop
                        </p>
                        <p class="text-xs text-gray-500">BPMN or XML files only</p>
                    </div>
                    <div id="file-info" class="hidden mt-4 p-3 bg-blue-50 rounded-md">
                        <p class="text-sm text-blue-800" id="file-name"></p>
                    </div>
                </div>
            </div>

            <div class="flex justify-between">
                <a href="{{ route('workflow.shar.workflows.index') }}" class="px-4 py-2 text-gray-600 hover:text-gray-800 transition-colors">
                    ← Back to Workflows
                </a>
                <button 
                    type="submit" 
                    class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors"
                    id="submit-btn"
                >
                    Create Workflow
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Success/Error Messages -->
<div id="message-container" class="fixed top-4 right-4 z-50"></div>

<script>
let selectedFile = null;

function handleFileSelect(input) {
    const file = input.files[0];
    if (file) {
        selectedFile = file;
        document.getElementById('file-info').classList.remove('hidden');
        document.getElementById('file-name').textContent = file.name;
        
        // Read file content
        const reader = new FileReader();
        reader.onload = function(e) {
            // Store the file content for form submission
            selectedFile.content = e.target.result;
        };
        reader.readAsText(file);
    }
}

function showMessage(message, type = 'success') {
    const container = document.getElementById('message-container');
    const alertClass = type === 'success' ? 'bg-green-500' : 'bg-red-500';
    
    const messageEl = document.createElement('div');
    messageEl.className = `${alertClass} text-white px-6 py-3 rounded-md shadow-lg mb-4`;
    messageEl.textContent = message;
    
    container.appendChild(messageEl);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        messageEl.remove();
    }, 5000);
}

document.getElementById('workflow-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    if (!selectedFile || !selectedFile.content) {
        showMessage('Please select a BPMN file', 'error');
        return;
    }
    
    const formData = {
        name: document.getElementById('name').value,
        description: document.getElementById('description').value,
        bpmn_xml: selectedFile.content
    };
    
    const submitBtn = document.getElementById('submit-btn');
    submitBtn.disabled = true;
    submitBtn.textContent = 'Creating...';
    
    fetch('/api/shar/workflows', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
        },
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showMessage('Workflow created successfully!');
            setTimeout(() => {
                window.location.href = '{{ route("workflow.shar.workflows.index") }}';
            }, 1500);
        } else {
            showMessage(data.message || 'Failed to create workflow', 'error');
        }
    })
    .catch(error => {
        showMessage('An error occurred while creating the workflow', 'error');
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.textContent = 'Create Workflow';
    });
});

// Drag and drop functionality
const dropZone = document.getElementById('file-drop-zone');

['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
    dropZone.addEventListener(eventName, preventDefaults, false);
});

function preventDefaults(e) {
    e.preventDefault();
    e.stopPropagation();
}

['dragenter', 'dragover'].forEach(eventName => {
    dropZone.addEventListener(eventName, highlight, false);
});

['dragleave', 'drop'].forEach(eventName => {
    dropZone.addEventListener(eventName, unhighlight, false);
});

function highlight(e) {
    dropZone.classList.add('border-blue-400', 'bg-blue-50');
}

function unhighlight(e) {
    dropZone.classList.remove('border-blue-400', 'bg-blue-50');
}

dropZone.addEventListener('drop', handleDrop, false);

function handleDrop(e) {
    const dt = e.dataTransfer;
    const files = dt.files;
    
    if (files.length > 0) {
        const file = files[0];
        if (file.name.endsWith('.bpmn') || file.name.endsWith('.xml')) {
            document.getElementById('bpmn_file').files = files;
            handleFileSelect(document.getElementById('bpmn_file'));
        } else {
            showMessage('Please upload a valid BPMN or XML file', 'error');
        }
    }
}
</script>
@endsection