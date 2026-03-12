@php
    $id = $attributes->get('id', 'editor-' . uniqid());
    $name = $attributes->get('name', $id);
    $value = $attributes->get('value', null);
    $placeholder = $attributes->get('placeholder', 'Write something...');
    $minHeight = $attributes->get('min-height', 200);
    $disabled = $attributes->get('disabled', false);
    $toolbar = $attributes->get('toolbar', ['bold', 'italic', 'underline', 'strike', 'link', 'ol', 'ul', 'blockquote', 'code']);
    $toolbarMap = [
        'bold' => '<button type="button" class="size-8 inline-flex justify-center items-center gap-x-2 text-sm font-semibold rounded-full border border-transparent text-gray-800 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none dark:text-white dark:hover:bg-neutral-700 dark:focus:bg-neutral-700" data-action="bold"><svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 12a4 4 0 0 0 0-8H6v8"/><path d="M15 20a4 4 0 0 0 0-8H6v8Z"/></svg></button>',
        'italic' => '<button type="button" class="size-8 inline-flex justify-center items-center gap-x-2 text-sm font-semibold rounded-full border border-transparent text-gray-800 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none dark:text-white dark:hover:bg-neutral-700 dark:focus:bg-neutral-700" data-action="italic"><svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" x2="10" y1="4" y2="4"/><line x1="14" x2="5" y1="20" y2="20"/><line x1="15" x2="9" y1="4" y2="20"/></svg></button>',
        'underline' => '<button type="button" class="size-8 inline-flex justify-center items-center gap-x-2 text-sm font-semibold rounded-full border border-transparent text-gray-800 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none dark:text-white dark:hover:bg-neutral-700 dark:focus:bg-neutral-700" data-action="underline"><svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 4v6a6 6 0 0 0 12 0V4"/><line x1="4" x2="20" y1="20" y2="20"/></svg></button>',
        'strike' => '<button type="button" class="size-8 inline-flex justify-center items-center gap-x-2 text-sm font-semibold rounded-full border border-transparent text-gray-800 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none dark:text-white dark:hover:bg-neutral-700 dark:focus:bg-neutral-700" data-action="strikethrough"><svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 4H9a3 3 0 0 0-2.83 4"/><path d="M14 12a4 4 0 0 1 0 8H6"/><line x1="4" x2="20" y1="12" y2="12"/></svg></button>',
        'ol' => '<button type="button" class="size-8 inline-flex justify-center items-center gap-x-2 text-sm font-semibold rounded-full border border-transparent text-gray-800 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none dark:text-white dark:hover:bg-neutral-700 dark:focus:bg-neutral-700" data-action="insertOrderedList"><svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="10" x2="21" y1="6" y2="6"/><line x1="10" x2="21" y1="12" y2="12"/><line x1="10" x2="21" y1="18" y2="18"/><path d="M4 6h1v4"/><path d="M4 10h2"/><path d="M6 18H4c0-1 2-2 2-3s-1-1.5-2-1"/></svg></button>',
        'ul' => '<button type="button" class="size-8 inline-flex justify-center items-center gap-x-2 text-sm font-semibold rounded-full border border-transparent text-gray-800 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none dark:text-white dark:hover:bg-neutral-700 dark:focus:bg-neutral-700" data-action="insertUnorderedList"><svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="8" x2="21" y1="6" y2="6"/><line x1="8" x2="21" y1="12" y2="12"/><line x1="8" x2="21" y1="18" y2="18"/><line x1="3" x2="3.01" y1="6" y2="6"/><line x1="3" x2="3.01" y1="12" y2="12"/><line x1="3" x2="3.01" y1="18" y2="18"/></svg></button>',
        'blockquote' => '<button type="button" class="size-8 inline-flex justify-center items-center gap-x-2 text-sm font-semibold rounded-full border border-transparent text-gray-800 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none dark:text-white dark:hover:bg-neutral-700 dark:focus:bg-neutral-700" data-action="formatBlock" data-value="blockquote"><svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21c3 0 7-1 7-8V5c0-1.25-.756-2.017-2-2H4c-1.25 0-2 .75-2 1.972V11c0 1.25.75 2 2 2 1 0 1 0 1 1v1c0 1-1 2-2 2s-1 .008-1 1.031V20c0 1 0 1 1 1z"/><path d="M15 21c3 0 7-1 7-8V5c0-1.25-.757-2.017-2-2h-4c-1.25 0-2 .75-2 1.972V11c0 1.25.75 2 2 2h.75c0 2.25.25 4-2.75 4v3c0 1 0 1 1 1z"/></svg></button>',
        'code' => '<button type="button" class="size-8 inline-flex justify-center items-center gap-x-2 text-sm font-semibold rounded-full border border-transparent text-gray-800 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none dark:text-white dark:hover:bg-neutral-700 dark:focus:bg-neutral-700" data-action="formatBlock" data-value="pre"><svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg></button>',
        'link' => '<button type="button" class="size-8 inline-flex justify-center items-center gap-x-2 text-sm font-semibold rounded-full border border-transparent text-gray-800 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none dark:text-white dark:hover:bg-neutral-700 dark:focus:bg-neutral-700" data-action="createLink"><svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg></button>',
    ];
@endphp

<div class="border border-gray-200 rounded-xl overflow-hidden dark:border-neutral-700" {{ $attributes->except(['value', 'toolbar', 'min-height', 'disabled']) }}>
    {{-- Toolbar --}}
    <div class="flex items-center gap-x-0.5 border-b border-gray-200 p-2 dark:border-neutral-700">
        @foreach($toolbar as $tool)
            @if(isset($toolbarMap[$tool]))
                {!! $toolbarMap[$tool] !!}
            @endif
        @endforeach
    </div>

    {{-- Editor Area --}}
    <div id="{{ $id }}-editor"
         class="p-4 text-sm text-gray-800 focus:outline-none dark:text-neutral-200 [&_blockquote]:border-l-4 [&_blockquote]:border-gray-300 [&_blockquote]:pl-4 [&_blockquote]:italic [&_pre]:bg-gray-100 [&_pre]:p-3 [&_pre]:rounded-lg [&_pre]:font-mono [&_pre]:text-xs dark:[&_pre]:bg-neutral-800 [&_ol]:list-decimal [&_ol]:ps-5 [&_ul]:list-disc [&_ul]:ps-5 [&_a]:text-blue-600 [&_a]:underline"
         contenteditable="{{ $disabled ? 'false' : 'true' }}"
         style="min-height: {{ $minHeight }}px"
         data-placeholder="{{ $placeholder }}"
    >{!! $value !!}</div>

    {{-- Hidden input for form submission --}}
    <textarea id="{{ $id }}" name="{{ $name }}" class="hidden">{!! $value !!}</textarea>
</div>

@pushOnce('script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('[id$="-editor"][contenteditable="true"]').forEach(function(editor) {
        var textareaId = editor.id.replace('-editor', '');
        var textarea = document.getElementById(textareaId);
        var toolbar = editor.previousElementSibling;

        // Sync content to hidden textarea
        editor.addEventListener('input', function() {
            if (textarea) textarea.value = editor.innerHTML;
        });

        // Toolbar button actions
        if (toolbar) {
            toolbar.querySelectorAll('button[data-action]').forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    var action = this.getAttribute('data-action');
                    var val = this.getAttribute('data-value');
                    if (action === 'createLink') {
                        val = prompt('Enter URL:', 'https://');
                        if (val) document.execCommand(action, false, val);
                    } else if (action === 'formatBlock') {
                        document.execCommand(action, false, val);
                    } else {
                        document.execCommand(action, false, null);
                    }
                    editor.focus();
                    if (textarea) textarea.value = editor.innerHTML;
                });
            });
        }

        // Placeholder
        var ph = editor.getAttribute('data-placeholder');
        if (ph && !editor.textContent.trim()) {
            editor.classList.add('before:content-[attr(data-placeholder)]', 'before:text-gray-400', 'empty:before:block', 'before:dark:text-neutral-500');
        }
    });
});
</script>
@endPushOnce
