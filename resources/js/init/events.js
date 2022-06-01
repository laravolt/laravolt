window.addEventListener('laravolt.toast', function (e) {
    $('body').toast(JSON.parse(e.detail));
});

// Reinitialization all Laravolt components when HTML fragment updated
if (typeof Livewire !== "undefined") {
    Livewire.hook('message.processed', (el, component) => {
        Laravolt.init($('[wire\\:id="' + component.id + '"]'));
    })
}

if (typeof up !== "undefined" && window.document.documentElement.dataset.spa) {
    up.fragment.config.runScripts = true;
    up.fragment.config.navigateOptions.cache = false;

    let firstTimeVisit = true;
    up.compiler('main.content', function (element) {
        Laravolt.init($(element));

        document.body.dataset.theme = element.dataset.theme;
        document.documentElement.dataset.fontSize = element.dataset.fontSize;
        document.documentElement.style.setProperty('--app-accent-color', 'var(--' + element.dataset.accentColor + ')');

        if (!firstTimeVisit && window.Livewire) {
            window.Livewire.restart();
        }
        firstTimeVisit = false;
    })
    up.link.config.followSelectors.push('a[href]');
    up.form.config.submitSelectors.push(['form']);
}
