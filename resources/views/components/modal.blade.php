<div class="ui modal small visible active top aligned transition scale in"
     x-show="activeModal == '{{ $this->key }}'"
     x-ref="{{ $this->key }}"
>
    <i class="close icon" @click="close()"></i>
    {{ $slot }}
</div>
