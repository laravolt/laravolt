// Returns a function, that, as long as it continues to be invoked, will not
// be triggered. The function will be called after it stops being called for
// N milliseconds. If `immediate` is passed, trigger the function on the
// leading edge, instead of the trailing.
function debounce(func, wait, immediate) {
    var timeout;
    return function () {
        var context = this, args = arguments;
        var later = function () {
            timeout = null;
            if (!immediate) func.apply(context, args);
        };
        var callNow = immediate && !timeout;
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
        if (callNow) func.apply(context, args);
    };
};

$(function () {

    const sidebar = $('[data-role="sidebar"] .sidebar__scroller');
    if (sidebar.length > 0) {
        new SimpleBar(sidebar[0]);

        $(window).resize(function () {
            if (document.body.clientWidth < 991) {
                $('#topbar').addClass('full');
                localStorage.setItem("layout-mode", "box");
                sidebar.parent().addClass('hide').removeClass('show');
            }
        });

        const sidebarVisibilitySwitcher = $('[data-role="sidebar-visibility-switcher"]');

        if (sidebarVisibilitySwitcher.length > 0) {
            sidebarVisibilitySwitcher.on('click', function () {
                if (document.body.clientWidth >= 991) {
                    sidebar.parent().toggleClass('show').toggleClass('hide');
                    if (sidebar.parent().hasClass('show')) {
                        $('#topbar').removeClass('full');
                        localStorage.setItem("layout-mode", "box");
                    } else {
                        $('#topbar').addClass('full');
                        localStorage.setItem("layout-mode", "full");
                    }
                }
            });

            sidebarVisibilitySwitcher.on('mouseover', function (e) {
                if (sidebar.parent().hasClass('hide')) {
                    sidebar.parent().addClass('floated');
                }
            });
            sidebarVisibilitySwitcher.on('mouseleave', function (e) {
                if (sidebar.parent().hasClass('hide')) {
                    sidebar.parent().removeClass('floated');
                }
            });

            sidebar.parent().on('mouseover', function (e) {
                if (sidebar.parent().hasClass('hide')) {
                    sidebar.parent().addClass('floated');
                }
            });
            sidebar.parent().on('mouseleave', function (e) {
                if (sidebar.parent().hasClass('hide')) {
                    sidebar.parent().removeClass('floated');
                }
            });

        }

        // Hide sidebar ketika user click element di luar sidebar ketika sidebar ditampilkan di perangkat mobile
        $(document).click(function (event) {
            if ($('nav.sidebar').hasClass('show')) {
                if (!$(event.target).closest('nav.sidebar').length && !$(event.target).closest('[data-role="sidebar-visibility-switcher"]').length) {
                    $('nav.sidebar').removeClass('show');
                }
            }
        });

        // Track scroll position
        $('#sidebar .simplebar-scroll-content').scroll(debounce(function () {
                $('#sidebar').data('scroll', $('#sidebar .simplebar-scroll-content').scrollTop());
            }, 500)
        );

    }

    $('[data-role="sidenav"]').accordion({
        selector: {
            trigger: '.title:not(.empty)'
        }
    });

    $('#sidebar').on('click', 'a.item, a.title.empty', function (e) {
        $(e.delegateTarget).find('.selected').removeClass('selected');
        $(this).addClass('selected');
    })
});
