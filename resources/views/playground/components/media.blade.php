<x-volt-panel title="Media Library">
    @if(method_exists(auth()->user(), 'getMedia'))
        <x-volt-media-library :collection="auth()->user()->getMedia()" :delete="true"/>
    @else
        <div class="text-center py-8">
            <p class="text-sm text-gray-500 dark:text-neutral-400">Media library is not available. The User model needs to implement HasMedia interface.</p>
        </div>
    @endif
</x-volt-panel>
