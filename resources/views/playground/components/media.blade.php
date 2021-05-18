<x-volt-panel title="Media Library">
    <x-volt-media-library :collection="auth()->user()->getMedia()" :delete="true"/>
</x-volt-panel>
