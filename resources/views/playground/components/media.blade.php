<x-laravolt::panel title="Media Library">
    <x-laravolt::media-library :collection="auth()->user()->getMedia()" :delete="true"/>
</x-laravolt::panel>
