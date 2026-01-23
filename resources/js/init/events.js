window.addEventListener('laravolt.toast', function (e) {
    $('body').toast(JSON.parse(e.detail));
});

// Reinitialization all Laravolt components when HTML fragment updated
if (typeof Livewire !== "undefined") {
    Livewire.hook('morph.updated', ({ el, component }) => {
        Laravolt.init($('[wire\\:id="' + component.id + '"]'));
    })
}

if (typeof up !== "undefined" && window.document.documentElement.dataset.spa) {
    up.fragment.config.runScripts = true;
    up.fragment.config.navigateOptions.cache = false;

    let firstTimeVisit = true;
    up.compiler('[up-main="modal"]', function (element) {
        Laravolt.init($(element));
        document.documentElement.dataset.theme = element.dataset.theme;
        document.documentElement.dataset.fontSize = element.dataset.fontSize;
        document.documentElement.dataset.sidebarDensity = element.dataset.sidebarDensity;
        document.documentElement.style.setProperty('--app-accent-color', 'var(--' + element.dataset.accentColor + ')');

        if (!firstTimeVisit && window.Livewire) {
            window.Livewire.restart();
        }
        firstTimeVisit = false;
    })
    up.link.config.followSelectors.push('a[href]');
    up.form.config.submitSelectors.push(['form']);

    //refresh page on error 500
    up.on('up:fragment:loaded', (event) => {
        let isErrorPage = event.response.status === 500;

        if (isErrorPage) {
            // Prevent the fragment update and don't update browser history
            event.preventDefault()

            // Make a full page load for the same request.
            event.request.loadPage()
        }
    })
} else {
    $(function(){
        Laravolt.init($('body'));
    });
}
