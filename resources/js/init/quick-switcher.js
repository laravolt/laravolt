$(function () {

    key('⌘+k, ctrl+k', function () {
        var modal = $('[data-role="quick-switcher-modal"]');

        modal.modal({
            onHide: function () {
                $('[data-role="quick-menu-searchbox"]').val("").trigger('keyup');
            }
        }).modal('show');
    });

    $('[data-role="quick-menu-searchbox"]').on('keyup', function (e) {

        var keyword = $(e.currentTarget).val();
        $('[data-role="quick-menu-searchbox"]').val(keyword);

        $('[data-role="quick-menu"] .items').html("");

        if (keyword == '') {
            $('[data-role="original-menu"]').show();
        } else {
            $('[data-role="original-menu"]').hide();
            var items = [];
            $('[data-role="original-menu"] a').each(function (index, elm) {
                items.push({text: $(elm).html(), url: $(elm).attr('href')});
            });

            var options = {
                tokenize: true,
                threshold: 0.5,
                keys: ['text']
            }
            var fuse = new Fuse(items, options)
            var result = fuse.search(keyword);
            var matches = '';
            for (var i in result) {
                var item = result[i];
                matches += "<a class='title' href='" + item.url + "'>" + item.text + "</a>";
            }
            $('[data-role="quick-menu"] .items').append(matches);
        }
    });

    var quickSwitcherDropdown = $('[data-role="quick-switcher-dropdown"]');
    $('[data-role="original-menu"] a').each(function (index, elm) {
        var parent = $(elm).data('parent');
        var child = $(elm).html();
        var label = child;
        if (parent) {
            label += '<div class="ui mini label right floated">' + parent + '</div>';
        }
        var option = $('<option>').attr('value', $(elm).attr('href')).html(label);
        quickSwitcherDropdown.append(option);
    });

    quickSwitcherDropdown.dropdown({
        fullTextSearch: true,
        forceSelection: false,
        selectOnKeydown: false,
        match: 'text',
        action: function (text, value) {
            window.location.href = value;
        }
    });
});
