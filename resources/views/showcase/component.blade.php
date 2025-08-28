@extends('laravolt::layout.app')

@section('title', $componentData['name'] . ' Component')

@push('styles')
<style>
    .code-block {
        background: #1e293b;
        color: #e2e8f0;
        border-radius: 0.5rem;
        overflow-x: auto;
    }
    .code-block pre {
        margin: 0;
        padding: 1rem;
        font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
        font-size: 0.875rem;
        line-height: 1.5;
    }
    .demo-section {
        background: #f8fafc;
        border: 2px dashed #e2e8f0;
        border-radius: 0.5rem;
        padding: 2rem;
        margin: 1rem 0;
    }
    .dark .demo-section {
        background: #1e293b;
        border-color: #475569;
    }
    .example-grid {
        display: grid;
        gap: 1rem;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    }
</style>
@endpush

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Breadcrumb -->
    <nav class="flex mb-8" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ route('platform::components.index') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-neutral-400 dark:hover:text-white">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
                    </svg>
                    Components
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                    </svg>
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2 dark:text-neutral-400">{{ $componentData['category'] }}</span>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                    </svg>
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2 dark:text-neutral-400">{{ $componentData['name'] }}</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Component Header -->
    <div class="mb-12">
        <div class="flex items-center gap-4 mb-4">
            <h1 class="text-4xl font-bold text-gray-900 dark:text-white">{{ $componentData['name'] }}</h1>
            <span class="bg-{{ $componentData['category'] === 'Form' ? 'green' : 'blue' }}-100 text-{{ $componentData['category'] === 'Form' ? 'green' : 'blue' }}-800 text-sm font-medium px-3 py-1 rounded-full dark:bg-{{ $componentData['category'] === 'Form' ? 'green' : 'blue' }}-900 dark:text-{{ $componentData['category'] === 'Form' ? 'green' : 'blue' }}-300">
                {{ $componentData['category'] }}
            </span>
        </div>

        <p class="text-xl text-gray-600 dark:text-neutral-400 mb-6">
            {{ $componentData['description'] }}
        </p>

        <!-- Quick Info -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white dark:bg-neutral-800 rounded-lg border border-gray-200 dark:border-neutral-700 p-4">
                <h3 class="font-semibold text-gray-900 dark:text-white mb-2">Variants</h3>
                <div class="flex flex-wrap gap-1">
                    @foreach($componentData['variants'] as $variant)
                        <span class="bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded dark:bg-neutral-700 dark:text-neutral-300">{{ $variant }}</span>
                    @endforeach
                </div>
            </div>

            @if(!empty($componentData['sizes']))
                <div class="bg-white dark:bg-neutral-800 rounded-lg border border-gray-200 dark:border-neutral-700 p-4">
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-2">Sizes</h3>
                    <div class="flex flex-wrap gap-1">
                        @foreach($componentData['sizes'] as $size)
                            <span class="bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded dark:bg-neutral-700 dark:text-neutral-300">{{ $size }}</span>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="bg-white dark:bg-neutral-800 rounded-lg border border-gray-200 dark:border-neutral-700 p-4">
                <h3 class="font-semibold text-gray-900 dark:text-white mb-2">Features</h3>
                <div class="space-y-1">
                    @foreach($componentData['features'] as $feature)
                        <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-neutral-400">
                            <svg class="w-3 h-3 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            {{ $feature }}
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Component Examples -->
    <div class="space-y-12">
        @if($component === 'alert' && !empty($sampleData['examples']))
            <section>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Examples</h2>

                <div class="space-y-8">
                    @foreach($sampleData['examples'] as $example)
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3 capitalize">{{ $example['variant'] }} Alert</h3>
                            <!-- Demo -->
                            <div class="demo-section">
                                <x-volt-alert
                                    variant="{{ $example['variant'] }}"
                                    title="{{ $example['title'] }}"
                                    message="{{ $example['message'] }}"
                                    :dismissible="{{ $example['dismissible'] }}"
                                />
                            </div>

                            <!-- Code -->
                            <div class="code-block">
                                <pre><code>{!! '&lt;x-volt-alert
    variant=&quot;' . $example['variant'] . '&quot;
    title=&quot;' . $example['title'] . '&quot;
    message=&quot;' . $example['message'] . '&quot;
    :dismissible=&quot;' . ($example['dismissible'] ? 'true' : 'false') . '&quot;
/&gt;' !!}</code></pre>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        @if($component === 'button' && !empty($sampleData['examples']))
            <section>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Button Variants</h2>

                <div class="demo-section">
                    <div class="flex flex-wrap gap-4">
                        @foreach($sampleData['examples'] as $example)
                            <x-volt-button
                                variant="{{ $example['variant'] }}"
                                :loading="{{ $example['loading'] ?? false }}"
                            >
                                {{ $example['label'] }}
                            </x-volt-button>
                        @endforeach
                    </div>
                </div>

                <!-- Size Examples -->
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mt-8 mb-4">Button Sizes</h3>
                <div class="demo-section">
                    <div class="flex flex-wrap items-center gap-4">
                        @foreach(['2xs', 'xs', 'sm', 'md', 'lg', 'xl'] as $size)
                            <x-volt-button size="{{ $size }}" variant="primary">
                                Size {{ $size }}
                            </x-volt-button>
                        @endforeach
                    </div>
                </div>

                <!-- Code Example -->
                <div class="code-block">
                    <pre><code>&lt;x-volt-button variant="primary" size="md"&gt;
    Primary Button
&lt;/x-volt-button&gt;

&lt;x-volt-button variant="outline" :loading="true"&gt;
    Loading Button
&lt;/x-volt-button&gt;</code></pre>
                </div>
            </section>
        @endif

        @if($component === 'accordion' && !empty($sampleData['items']))
            <section>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Accordion Example</h2>

                <div class="demo-section">
                    <x-volt-accordion
                        :items="$sampleData['items']"
                        variant="default"
                        :allow-multiple="false"
                    />
                </div>

                <div class="code-block">
                    <pre><code>&lt;x-volt-accordion
    :items="[
        [
            'title' => 'Getting Started',
            'content' => 'Learn how to integrate components...',
            'open' => true
        ],
        [
            'title' => 'Advanced Features',
            'content' => 'Explore advanced features...'
        ]
    ]"
    variant="default"
    :allow-multiple="false"
/&gt;</code></pre>
                </div>
            </section>
        @endif

        @if($component === 'timeline' && !empty($sampleData['items']))
            <section>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Timeline Example</h2>

                <div class="demo-section">
                    <x-volt-timeline
                        :items="$sampleData['items']"
                        variant="primary"
                        :show-connector="true"
                    />
                </div>

                <div class="code-block">
                    <pre><code>&lt;x-volt-timeline
    :items="[
        [
            'title' => 'Project Planning',
            'description' => 'Initial project setup',
            'timestamp' => '2024-01-15',
            'status' => 'completed'
        ],
        [
            'title' => 'Development Phase',
            'description' => 'Core functionality implementation',
            'timestamp' => '2024-02-01',
            'status' => 'current'
        ]
    ]"
    variant="primary"
    :show-connector="true"
/&gt;</code></pre>
                </div>
            </section>
        @endif

        @if($component === 'steps' && !empty($sampleData))
            <section>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Steps Example</h2>

                <div class="demo-section">
                    <x-volt-steps
                        :steps="$sampleData['steps']"
                        :current-step="$sampleData['currentStep']"
                        :clickable="false"
                    />
                </div>

                <div class="code-block">
                    <pre><code>&lt;x-volt-steps
    :steps="[
        ['title' => 'Account Setup', 'description' => 'Create your account'],
        ['title' => 'Profile Information', 'description' => 'Add your details'],
        ['title' => 'Preferences', 'description' => 'Configure settings'],
        ['title' => 'Completion', 'description' => 'Review and finish']
    ]"
    :current-step="2"
    :clickable="true"
/&gt;</code></pre>
                </div>
            </section>
        @endif

        @if($component === 'rating' && !empty($sampleData['examples']))
            <section>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Rating Examples</h2>

                <div class="space-y-6">
                    @foreach($sampleData['examples'] as $index => $example)
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">
                                @if($example['readonly'])
                                    Read-only Rating
                                @else
                                    Interactive Rating
                                @endif
                            </h3>

                            <div class="demo-section">
                                <x-volt-rating
                                    :value="$example['value']"
                                    :max="$example['max'] ?? 5"
                                    :readonly="$example['readonly']"
                                    variant="{{ $example['variant'] ?? 'yellow' }}"
                                    :show-count="{{ $example['showCount'] ?? false }}"
                                    :count="{{ $example['count'] ?? null }}"
                                />
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="code-block">
                    <pre><code>&lt;x-volt-rating
    :value="4.5"
    :max="5"
    variant="yellow"
    :readonly="true"
    :show-count="true"
    :count="128"
/&gt;</code></pre>
                </div>
            </section>
        @endif

        <!-- Default Examples for Other Components -->
        @if(!in_array($component, ['alert', 'button', 'accordion', 'timeline', 'steps', 'rating']))
            <section>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Basic Example</h2>

                <div class="demo-section">
                    @switch($component)
                        @case('pin-code')
                            <x-volt-pin-code :length="4" :mask="false" size="md" />
                            @break
                        @case('copy-markup')
                            <x-volt-copy-markup
                                content="<div class='example'>Hello World!</div>"
                                language="html"
                                :show-copy-button="true"
                            />
                            @break
                        @case('scroll-indicator')
                            <x-volt-scroll-indicator variant="top" color="blue" size="md" />
                            @break
                        @case('notification')
                            <x-volt-notification
                                title="Success!"
                                message="Your changes have been saved successfully."
                                variant="success"
                                :dismissible="true"
                            />
                            @break
                        @default
                            <p class="text-gray-600 dark:text-neutral-400">
                                Component examples will be displayed here.
                                Use the basic component syntax: <code class="bg-gray-100 px-2 py-1 rounded text-sm dark:bg-neutral-700">&lt;x-volt-{{ $component }} /&gt;</code>
                            </p>
                    @endswitch
                </div>

                <div class="code-block">
                    <pre><code>{!! '&lt;x-volt-' . $component . ' /&gt;' !!}</code></pre>
                </div>
            </section>
        @endif

        <!-- Usage Instructions -->
        <section>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Usage</h2>

            <div class="bg-white dark:bg-neutral-800 rounded-lg border border-gray-200 dark:border-neutral-700 p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Basic Usage</h3>
                <div class="code-block">
                    <pre><code>{!! '&lt;x-volt-' . $component . ' /&gt;' !!}</code></pre>
                </div>

                @if(!empty($componentData['variants']))
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3 mt-6">With Variants</h3>
                    <div class="code-block">
                        <pre><code>{!! '&lt;x-volt-' . $component . ' variant=&quot;' . $componentData['variants'][0] . '&quot; /&gt;' !!}</code></pre>
                    </div>
                @endif

                @if(!empty($componentData['sizes']))
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3 mt-6">With Sizes</h3>
                    <div class="code-block">
                        <pre><code>{!! '&lt;x-volt-' . $component . ' size=&quot;' . $componentData['sizes'][0] . '&quot; /&gt;' !!}</code></pre>
                    </div>
                @endif
            </div>
        </section>

        <!-- API Reference -->
        <section>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">API Reference</h2>

            <div class="bg-white dark:bg-neutral-800 rounded-lg border border-gray-200 dark:border-neutral-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-neutral-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Component Properties</h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700">
                        <thead class="bg-gray-50 dark:bg-neutral-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-neutral-300">Property</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-neutral-300">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-neutral-300">Default</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-neutral-300">Description</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 dark:bg-neutral-800 dark:divide-neutral-700">
                            @if(!empty($componentData['variants']))
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">variant</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-neutral-400">string</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-neutral-400">{{ $componentData['variants'][0] }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500 dark:text-neutral-400">Component visual variant</td>
                                </tr>
                            @endif

                            @if(!empty($componentData['sizes']))
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">size</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-neutral-400">string</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-neutral-400">md</td>
                                    <td class="px-6 py-4 text-sm text-gray-500 dark:text-neutral-400">Component size</td>
                                </tr>
                            @endif

                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">class</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-neutral-400">string</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-neutral-400">-</td>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-neutral-400">Additional CSS classes</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>

    <!-- Navigation -->
    <div class="flex justify-between items-center mt-16 pt-8 border-t border-gray-200 dark:border-neutral-700">
        <a href="{{ route('platform::components.index') }}" class="inline-flex items-center gap-2 text-blue-600 hover:text-blue-700 font-medium dark:text-blue-400 dark:hover:text-blue-300">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Back to Components
        </a>

        <button onclick="copyToClipboard('&lt;x-volt-{{ $component }} /&gt;')" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200">
            Copy Basic Usage
        </button>
    </div>
</div>

@push('scripts')
<script>
function copyToClipboard(text) {
    if (navigator.clipboard) {
        navigator.clipboard.writeText(text).then(() => {
            showNotification('Copied to clipboard!', 'success');
        });
    } else {
        const textArea = document.createElement('textarea');
        textArea.value = text;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        showNotification('Copied to clipboard!', 'success');
    }
}

function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 px-4 py-2 rounded-lg text-white text-sm font-medium transform transition-all duration-300 ${
        type === 'success' ? 'bg-green-600' : 'bg-blue-600'
    }`;
    notification.textContent = message;
    notification.style.transform = 'translateX(100%)';

    document.body.appendChild(notification);

    setTimeout(() => {
        notification.style.transform = 'translateX(0)';
    }, 10);

    setTimeout(() => {
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
}
</script>
@endpush
@endsection