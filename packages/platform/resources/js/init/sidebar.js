$(function () {

    var sidebar = $('[data-role="sidebar"]');
    if (sidebar.length > 0) {
        new SimpleBar(sidebar[0]);

        var sidebarVisibilitySwitcher = $('[data-role="sidebar-visibility-switcher"]');
        if (sidebarVisibilitySwitcher.length > 0) {
            sidebarVisibilitySwitcher.on('click', function () {
                sidebar.parent().toggleClass('show');
            });
        }

        $(document).click(function (event) {
            if ($('nav.sidebar').hasClass('show')) {
                if (!$(event.target).closest('nav.sidebar').length && !$(event.target).closest('[data-role="sidebar-visibility-switcher"]').length) {
                    $('nav.sidebar').removeClass('show');
                }
            }
        });
    }

    $('[data-role="sidebar-accordion"]').accordion({
        selector: {
            trigger: '.title:not(.empty)'
        }
    });
});
