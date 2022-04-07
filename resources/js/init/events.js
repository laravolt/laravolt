window.addEventListener('laravolt.toast', function (e) {
    $('body').toast(JSON.parse(e.detail));
});

if (typeof Livewire !== "undefined") {
    Livewire.hook('message.processed', (el, component) => {
        Laravolt.init($('[wire\\:id="' + component.id + '"]'));
    })
}
