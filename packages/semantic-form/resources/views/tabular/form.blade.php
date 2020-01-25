@php($id = 'multirow-'.md5(mt_rand()))
<div id="{{ $id }}">
    @livewire('semantic-form::multirow', $name, $schema, $source, $limit, $allowAddition, $allowRemoval)
</div>

@push('script')
    <script>
      document.addEventListener("livewire:load", function (event) {
        window.livewire.hook('afterDomUpdate', () => {
          Laravolt.init($('{{ $id }}'));
        });
      });
    </script>
@endpush
