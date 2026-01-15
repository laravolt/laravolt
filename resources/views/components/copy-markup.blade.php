@php
    $content = $attributes->get('content', '');
    $language = $attributes->get('language', 'html');
    $showCopyButton = $attributes->get('show-copy-button', true);
    $showLineNumbers = $attributes->get('show-line-numbers', false);
    $theme = $attributes->get('theme', 'light');
    $size = $attributes->get('size', 'md');
    $id = $attributes->get('id', 'copy-markup-' . uniqid());
    $attributes = $attributes->except(['content', 'language', 'show-copy-button', 'show-line-numbers', 'theme', 'size', 'id']);

    // Size variants for Preline UI v3.0
    $sizeClasses = [
        'sm' => 'text-xs',
        'md' => 'text-sm',
        'lg' => 'text-base'
    ];

    // Theme styles
    $themeClasses = [
        'light' => [
            'container' => 'bg-white border border-gray-200 dark:bg-neutral-800 dark:border-neutral-700',
            'header' => 'bg-gray-50 border-b border-gray-200 dark:bg-neutral-700 dark:border-neutral-600',
            'code' => 'bg-gray-50 dark:bg-neutral-800',
            'text' => 'text-gray-800 dark:text-neutral-200'
        ],
        'dark' => [
            'container' => 'bg-gray-900 border border-gray-700',
            'header' => 'bg-gray-800 border-b border-gray-700',
            'code' => 'bg-gray-900',
            'text' => 'text-gray-100'
        ]
    ];

    $currentSize = $sizeClasses[$size] ?? $sizeClasses['md'];
    $currentTheme = $themeClasses[$theme] ?? $themeClasses['light'];
    
    // Get content from slot if not provided in attributes
    if (empty($content) && $slot->isNotEmpty()) {
        $content = $slot->toHtml();
    }
    
    // Prepare content for display
    $displayContent = htmlspecialchars($content);
    $lines = explode("\n", $displayContent);
@endphp

<!-- Preline UI v3.0 Copy Markup Component -->
<div id="{{ $id }}" class="relative {{ $currentTheme['container'] }} rounded-lg overflow-hidden">
    @if($showCopyButton || $language)
        <!-- Header with language and copy button -->
        <div class="flex justify-between items-center px-4 py-2 {{ $currentTheme['header'] }}">
            @if($language)
                <span class="text-xs font-medium {{ $currentTheme['text'] }} uppercase tracking-wide">
                    {{ $language }}
                </span>
            @else
                <div></div>
            @endif
            
            @if($showCopyButton)
                <button 
                    type="button"
                    class="hs-copy-markup js-clipboard inline-flex items-center gap-x-1 text-xs font-medium rounded-lg border border-transparent {{ $currentTheme['text'] }} hover:bg-gray-100 focus:outline-none focus:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none dark:hover:bg-neutral-600 dark:focus:bg-neutral-600 py-1 px-2"
                    data-clipboard-target="#{{ $id }}-content"
                    data-clipboard-action="copy"
                    data-clipboard-success-text="Copied!"
                >
                    <svg class="shrink-0 size-3" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect width="14" height="14" x="8" y="8" rx="2" ry="2"></rect>
                        <path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"></path>
                    </svg>
                    <span class="js-clipboard-default">Copy</span>
                    <span class="js-clipboard-success hidden">Copied!</span>
                </button>
            @endif
        </div>
    @endif
    
    <!-- Code Content -->
    <div class="{{ $currentTheme['code'] }} overflow-x-auto">
        <pre id="{{ $id }}-content" class="p-4 {{ $currentSize }} {{ $currentTheme['text'] }} whitespace-pre-wrap"><code>@if($showLineNumbers)@foreach($lines as $index => $line)<span class="select-none text-gray-500 dark:text-neutral-500 mr-4">{{ str_pad($index + 1, 2, ' ', STR_PAD_LEFT) }}</span>{{ $line }}
@endforeach@else{{ $displayContent }}@endif</code></pre>
    </div>
</div>

@if($showCopyButton)
@pushOnce('copy-markup-scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Preline UI Copy Markup
    if (window.HSCopyMarkup) {
        window.HSCopyMarkup.autoInit();
    }
    
    // Clipboard functionality
    document.querySelectorAll('.js-clipboard').forEach(function(button) {
        button.addEventListener('click', function() {
            const target = document.querySelector(this.getAttribute('data-clipboard-target'));
            const action = this.getAttribute('data-clipboard-action');
            
            if (target && action === 'copy') {
                const textToCopy = target.textContent;
                
                // Use modern clipboard API
                if (navigator.clipboard) {
                    navigator.clipboard.writeText(textToCopy).then(function() {
                        showCopySuccess(button);
                    }).catch(function() {
                        fallbackCopy(textToCopy, button);
                    });
                } else {
                    fallbackCopy(textToCopy, button);
                }
            }
        });
    });
    
    function showCopySuccess(button) {
        const defaultText = button.querySelector('.js-clipboard-default');
        const successText = button.querySelector('.js-clipboard-success');
        
        if (defaultText && successText) {
            defaultText.classList.add('hidden');
            successText.classList.remove('hidden');
            
            setTimeout(function() {
                defaultText.classList.remove('hidden');
                successText.classList.add('hidden');
            }, 2000);
        }
    }
    
    function fallbackCopy(text, button) {
        const textArea = document.createElement('textarea');
        textArea.value = text;
        textArea.style.position = 'fixed';
        textArea.style.left = '-999999px';
        textArea.style.top = '-999999px';
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();
        
        try {
            document.execCommand('copy');
            showCopySuccess(button);
        } catch (err) {
            console.error('Failed to copy text: ', err);
        }
        
        document.body.removeChild(textArea);
    }
});
</script>
@endPushOnce
@endif