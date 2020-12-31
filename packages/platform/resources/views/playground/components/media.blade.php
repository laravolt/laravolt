<x-panel title="Media Library">
    <x-media-library :collection="auth()->user()->getMedia()" :delete="true"/>
</x-panel>
