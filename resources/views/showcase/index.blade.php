@extends('laravolt::layout.app')

@section('title', 'Component Showcase')

@push('styles')
<style>
    .component-card {
        transition: all 0.2s ease-in-out;
    }
    .component-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }
    .category-section {
        scroll-margin-top: 100px;
    }
</style>
@endpush

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="text-center mb-12">
        <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">
            Preline UI Component Showcase
        </h1>
        <p class="text-xl text-gray-600 dark:text-neutral-400 max-w-3xl mx-auto">
            Explore our comprehensive collection of Preline UI v3.0 components. Each component is built with accessibility, 
            modern design principles, and seamless Laravel integration in mind.
        </p>
        
        <!-- Quick Stats -->
        <div class="flex justify-center gap-8 mt-8">
            <div class="text-center">
                <div class="text-3xl font-bold text-blue-600 dark:text-blue-400">50+</div>
                <div class="text-sm text-gray-500 dark:text-neutral-400">Components</div>
            </div>
            <div class="text-center">
                <div class="text-3xl font-bold text-teal-600 dark:text-teal-400">8</div>
                <div class="text-sm text-gray-500 dark:text-neutral-400">Categories</div>
            </div>
            <div class="text-center">
                <div class="text-3xl font-bold text-purple-600 dark:text-purple-400">100%</div>
                <div class="text-sm text-gray-500 dark:text-neutral-400">Accessible</div>
            </div>
        </div>
    </div>

    <!-- Category Navigation -->
    <div class="sticky top-0 z-40 bg-white dark:bg-neutral-900 border-b border-gray-200 dark:border-neutral-700 mb-8 pb-4">
        <div class="flex flex-wrap gap-2 justify-center">
            @php
                $categories = collect([
                    'Form' => ['icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'count' => 13],
                    'Layout' => ['icon' => 'M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z', 'count' => 6],
                    'Navigation' => ['icon' => 'M4 6h16M4 12h16M4 18h16', 'count' => 5],
                    'Data Display' => ['icon' => 'M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 0V17m0-10a2 2 0 012 2h2a2 2 0 012-2V7', 'count' => 5],
                    'Feedback' => ['icon' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z', 'count' => 5],
                    'Utility' => ['icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z', 'count' => 4],
                ]);
            @endphp
            
            @foreach($categories as $category => $data)
                <a href="#{{ Str::slug($category) }}" 
                   class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors duration-200 dark:text-neutral-400 dark:hover:text-blue-400 dark:hover:bg-neutral-800">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $data['icon'] }}"/>
                    </svg>
                    {{ $category }}
                    <span class="bg-gray-100 text-gray-600 text-xs px-2 py-0.5 rounded-full dark:bg-neutral-700 dark:text-neutral-400">{{ $data['count'] }}</span>
                </a>
            @endforeach
        </div>
    </div>

    @php
        $controller = new \Laravolt\Platform\Controllers\ComponentShowcaseController();
        $components = $controller->getAvailableComponents();
        $groupedComponents = collect($components)->groupBy('category');
    @endphp

    <!-- Component Categories -->
    @foreach($groupedComponents as $category => $categoryComponents)
        <section id="{{ Str::slug($category) }}" class="category-section mb-16">
            <div class="flex items-center gap-4 mb-8">
                <h2 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $category }}</h2>
                <span class="bg-blue-100 text-blue-800 text-sm font-medium px-3 py-1 rounded-full dark:bg-blue-900 dark:text-blue-300">
                    {{ $categoryComponents->count() }} components
                </span>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($categoryComponents as $componentKey => $component)
                    <div class="component-card bg-white dark:bg-neutral-800 rounded-xl border border-gray-200 dark:border-neutral-700 p-6 hover:border-blue-300 dark:hover:border-blue-600">
                        <div class="flex items-start justify-between mb-4">
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                                {{ $component['name'] }}
                            </h3>
                            @if($component['category'] === 'Form')
                                <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full dark:bg-green-900 dark:text-green-300">Interactive</span>
                            @endif
                        </div>
                        
                        <p class="text-gray-600 dark:text-neutral-400 text-sm mb-4 line-clamp-2">
                            {{ $component['description'] }}
                        </p>
                        
                        <!-- Component Features -->
                        <div class="mb-4">
                            <div class="flex flex-wrap gap-1 mb-3">
                                @foreach(array_slice($component['variants'], 0, 3) as $variant)
                                    <span class="bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded dark:bg-neutral-700 dark:text-neutral-300">
                                        {{ $variant }}
                                    </span>
                                @endforeach
                                @if(count($component['variants']) > 3)
                                    <span class="text-gray-500 text-xs px-2 py-1">+{{ count($component['variants']) - 3 }} more</span>
                                @endif
                            </div>
                            
                            @if(!empty($component['sizes']))
                                <div class="text-xs text-gray-500 dark:text-neutral-400">
                                    Sizes: {{ implode(', ', array_slice($component['sizes'], 0, 4)) }}
                                    @if(count($component['sizes']) > 4)
                                        +{{ count($component['sizes']) - 4 }} more
                                    @endif
                                </div>
                            @endif
                        </div>
                        
                        <!-- Features List -->
                        @if(!empty($component['features']))
                            <div class="mb-4">
                                <div class="text-xs font-medium text-gray-700 dark:text-neutral-300 mb-2">Key Features:</div>
                                <div class="flex flex-wrap gap-1">
                                    @foreach(array_slice($component['features'], 0, 3) as $feature)
                                        <span class="inline-flex items-center gap-1 text-xs text-blue-600 dark:text-blue-400">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                            </svg>
                                            {{ $feature }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        
                        <!-- Action Button -->
                        <div class="flex gap-2">
                            <a href="{{ route('platform::components.show', $componentKey) }}" 
                               class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium py-2 px-4 rounded-lg transition-colors duration-200 text-center">
                                View Component
                            </a>
                            <button onclick="copyComponentUsage('{{ $componentKey }}')" 
                                    class="bg-gray-100 hover:bg-gray-200 text-gray-700 p-2 rounded-lg transition-colors duration-200 dark:bg-neutral-700 dark:hover:bg-neutral-600 dark:text-neutral-300"
                                    title="Copy usage example">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endforeach

    <!-- Footer -->
    <div class="text-center py-12 border-t border-gray-200 dark:border-neutral-700 mt-16">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Ready to get started?</h3>
        <p class="text-gray-600 dark:text-neutral-400 mb-4">
            Check out our documentation for installation and usage instructions.
        </p>
        <div class="flex justify-center gap-4">
            <a href="#" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200">
                View Documentation
            </a>
            <a href="#" class="border border-gray-300 hover:border-gray-400 text-gray-700 px-6 py-3 rounded-lg font-medium transition-colors duration-200 dark:border-neutral-600 dark:text-neutral-300 dark:hover:border-neutral-500">
                GitHub Repository
            </a>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Copy component usage example
function copyComponentUsage(component) {
    const usageExample = `<x-volt-${component} />`;
    
    if (navigator.clipboard) {
        navigator.clipboard.writeText(usageExample).then(() => {
            showNotification('Copied to clipboard!', 'success');
        });
    } else {
        // Fallback for older browsers
        const textArea = document.createElement('textarea');
        textArea.value = usageExample;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        showNotification('Copied to clipboard!', 'success');
    }
}

// Simple notification function
function showNotification(message, type = 'info') {
    // Create a simple toast notification
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 px-4 py-2 rounded-lg text-white text-sm font-medium transform transition-all duration-300 ${
        type === 'success' ? 'bg-green-600' : 'bg-blue-600'
    }`;
    notification.textContent = message;
    notification.style.transform = 'translateX(100%)';
    
    document.body.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
        notification.style.transform = 'translateX(0)';
    }, 10);
    
    // Remove after 3 seconds
    setTimeout(() => {
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
}

// Smooth scrolling for navigation links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});
</script>
@endpush
@endsection