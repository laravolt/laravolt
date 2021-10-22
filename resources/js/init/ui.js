class Laravolt {
    static init(root) {
        root.find('table.unstackable.responsive').basictable();

        root.find('.ui.checkbox').checkbox();

        root.find('.ui.dropdown:not(.simple):not(.tag)').each(function () {
            let elm = $(this);
            let options = {
                forceSelection: false,
                selectOnKeydown: false,
                fullTextSearch: true,
                action: elm.data('action') !== undefined ? elm.data('action') : 'activate',
            };
            if ($(this).hasClass('link')) {
                options.onChange = function (value, text, $selectedItem) {
                    window.location.href = value;
                };
            }

            if ($(this).data('ajax')) {
                let url = elm.data('api');
                let payload = elm.data('payload');
                let token = elm.data('token');

                options.minCharacters = 2;
                options.apiSettings = {
                    url: elm.data('api') + '?term={query}',
                    method: 'post',
                    data: {payload: payload, _token: token},
                }
            }

            $(this).dropdown(options);

        });

        root.find('.ui.dropdown.tag:not(.simple)').each(function () {

            let selected = false;
            if ($(this).data('value')) {
                let values = $(this).data('value').toString().split(',');
                if (values.length == 1 && values[0] == '') {

                } else {
                    selected = values;
                }
            }

            $(this).dropdown({
                forceSelection: false,
                allowAdditions: true,
                fullTextSearch: true,
                selectOnKeydown: false,
                // keys: {
                //     delimiter: 13
                // }
            }).dropdown('set selected', selected);
        });

        $.fn.destroyDropdown = function () {
            return $(this).each(function () {
                $(this).parent().dropdown('destroy').replaceWith($(this).addClass($(this).data('class')));
            });
        };

        let dependenciesSelect = [];
        let dependenciesInput = [];
        root.find('select[data-depend-on]').each(function (idx, elm) {
            let child = $(elm);
            let parentName = child.data('depend-on');
            let parent = $('[name=' + parentName + ']');

            if (parent.prop('tagName') == 'SELECT') {
                if (dependenciesSelect[parentName] === undefined) {
                    dependenciesSelect[parentName] = [];
                }
                dependenciesSelect[parentName].push(child);
            } else if (parent.prop('tagName') == 'INPUT') {
                if (dependenciesInput[parentName] === undefined) {
                    dependenciesInput[parentName] = [];
                }
                dependenciesInput[parentName].push(child);
            }
        });

        for (let parentName of Object.keys(dependenciesSelect)) {
            let parent = $('[name=' + parentName + ']');
            let children = dependenciesSelect[parentName];
            parent.destroyDropdown();
            parent.dropdown({
                forceSelection: false,
                selectOnKeydown: false,
                fullTextSearch: 'exact',
                onChange: function (value, text, $option) {

                    jQuery.each(children, function (idx, child) {

                        if (!value) {
                            child.dropdown('clear');
                            child.dropdown('setup menu', {values: []});
                        } else {
                            let url = child.data('api');
                            let payload = child.data('payload');
                            let token = child.data('token');

                            child.api({
                                url: url,
                                method: 'post',
                                data: {term: value, payload: payload, _token: token},
                                on: 'now',
                                beforeSend: function (settings) {
                                    child.dropdown('clear');
                                    child.parent().addClass('loading');

                                    return settings;
                                },
                                onSuccess: function (response, element, xhr) {
                                    let values = response.results;
                                    child.dropdown('change values', values);
                                },
                                onComplete: function (response, element, xhr) {
                                    child.parent().removeClass('loading');
                                },
                                onError: function (errorMessage, element, xhr) {
                                    if (typeof xhr.responseJSON.exception !== 'undefined') {
                                        alert(xhr.responseJSON.exception + ' in ' + xhr.responseJSON.file + ' line ' + xhr.responseJSON.line + ': ' + xhr.responseJSON.message);
                                    } else {
                                        alert('Something goes wrong with DropdownDB, but APP_DEBUG off. See application log (usually in storage/logs/laravel.log) for complete error message.');
                                    }
                                }
                            });
                        }

                    })
                }
            });
        }

        for (let parentName of Object.keys(dependenciesInput)) {
            let parent = root.find('[name=' + parentName + ']');
            let children = dependenciesInput[parentName];

            parent.on('change', function (e) {
                let value = $(e.currentTarget).val();
                jQuery.each(children, function (idx, child) {
                    if (!value) {
                        child.dropdown('clear');
                        child.dropdown('setup menu', {values: []});
                    } else {
                        let url = child.data('api');
                        let payload = child.data('payload');
                        let token = child.data('token');

                        child.api({
                            url: url,
                            method: 'post',
                            data: {term: value, payload: payload, _token: token},
                            on: 'now',
                            beforeSend: function (settings) {
                                child.dropdown('clear');
                                child.parent().addClass('loading');

                                return settings;
                            },
                            onSuccess: function (response, element, xhr) {
                                let values = response.results;
                                child.dropdown('change values', values);
                                if (values.length == 1) {
                                    child.dropdown('set selected', values[0].value);
                                }
                            },
                            onComplete: function (response, element, xhr) {
                                child.parent().removeClass('loading');
                            }
                        });
                    }

                })

            });

            if (parent.val()) {
                parent.trigger('change');
            }
        }

        root.find('.checkbox[data-toggle="checkall"]').each(function () {
            let $parent = $(this);
            let $childCheckbox = $(document).find($parent.data('selector'));
            let $storage = $(document).find($parent.data('storage'));

            $parent
                .checkbox({
                    // check all children
                    onChecked: function () {
                        $childCheckbox.checkbox('check');
                    },
                    // uncheck all children
                    onUnchecked: function () {
                        $childCheckbox.checkbox('uncheck');
                    }
                })
            ;

            $childCheckbox
                .checkbox({
                    // Fire on load to set parent value
                    fireOnInit: true,
                    // Change parent state on each child checkbox change
                    onChange: function () {
                        var
                            $parentCheckbox = $parent,
                            $checkbox = $childCheckbox,
                            allChecked = true,
                            allUnchecked = true,
                            ids = []
                        ;
                        // check to see if all other siblings are checked or unchecked
                        $checkbox.each(function () {
                            if ($(this).checkbox('is checked')) {
                                allUnchecked = false;
                                ids.push($(this).children().first().val());
                            } else {
                                allChecked = false;
                            }
                        });

                        $parentCheckbox.val(JSON.stringify(ids)).trigger('change');

                        // set parent checkbox state, but dont trigger its onChange callback
                        if (allChecked) {
                            $parentCheckbox.checkbox('set checked');
                        } else if (allUnchecked) {
                            $parentCheckbox.checkbox('set unchecked');
                        } else {
                            $parentCheckbox.checkbox('set indeterminate');
                        }
                    }
                })
            ;
        });

        root.find('.ui.input.calendar').each(function (idx, elm) {
            const $elm = $(elm);

            let type = $elm.data('calendar-type');
            if (!type) {
                type = 'date';
            }

            let format = $elm.data('calendar-format');
            if (!format) {
                format = 'YYYY-MM-DD';
            }

            $elm.calendar({
                type: type,
                ampm: false,
                selectAdjacentDays: true,
                onSelect: function (date, mode) {
                    elm.querySelector('input').dispatchEvent(new Event('input'));
                },
                formatter: {
                    date: function (date, settings) {
                        if (!date) {
                            return '';
                        }
                        const h = date.getHours();
                        const i = date.getMinutes();
                        const s = date.getSeconds();
                        const j = date.getDate();
                        const DD = ("0" + date.getDate()).slice(-2);
                        const d = DD;
                        const n = date.getMonth() + 1;
                        const MM = ("0" + (date.getMonth() + 1)).slice(-2);
                        const m = MM;
                        const MMMM = settings.text.months[date.getMonth()];
                        const M = settings.text.monthsShort[date.getMonth()];
                        const YY = date.getFullYear().toString().substr(2, 2);
                        const Y = date.getFullYear();
                        const YYYY = date.getFullYear();

                        return format
                            .replace('h', h)
                            .replace('i', i)
                            .replace('s', s)
                            .replace('j', j)
                            .replace('d', d)
                            .replace('n', n)
                            .replace('MMMM', MMMM)
                            .replace('MM', MM)
                            .replace('m', m)
                            .replace('M', M)
                            .replace('DD', DD)
                            .replace('YYYY', YYYY)
                            .replace('YY', YY)
                            .replace('Y', Y)
                            ;
                    }
                }
            })
            ;
        });

        root.find('input[type=file].uploader').each(function (idx, elm) {
            let extensions = $(elm).data('extensions');
            if (extensions) {
                extensions = extensions.split(',');
            } else {
                extensions = null;
            }

            let upload = null;

            if ($(elm).data('media-url')) {
                upload = {
                    url: $(elm).data('media-url'),
                    data: {_token: $(elm).data('token'), _key: $(elm).attr('name'), _action: 'upload'},
                    type: 'POST',
                    enctype: 'multipart/form-data',
                    start: true,
                    synchron: true,
                    chunk: false,
                    onSuccess: function (response, item, listEl, parentEl, newInputEl, inputEl, textStatus, jqXHR) {
                        if (response.success && response.files[0]) {
                            item.data.id = response.files[0].data.id ?? null;
                            item.local = response.files[0].file;
                            item.html.find('.fileuploader-action-remove').addClass('fileuploader-action-success');

                            setTimeout(function () {
                                item.html.find('.progress-bar2').fadeOut(400);
                            }, 400);

                            return true
                        }

                        return this.onError(item, listEl, parentEl, newInputEl, inputEl, jqXHR, textStatus, response.message);

                    },
                    onError: function (item, listEl, parentEl, newInputEl, inputEl, jqXHR, textStatus, errorThrown) {
                        let progressBar = item.html.find('.progress-bar2');

                        if (progressBar.length > 0) {
                            progressBar.find('span').html(0 + "%");
                            progressBar.find('.fileuploader-progressbar .bar').width(0 + "%");
                            item.html.find('.progress-bar2').fadeOut(400);
                        }

                        item.upload.status !== 'cancelled' && item.html.find('.fileuploader-action-retry').length === 0 ? item.html.find('.column-actions').prepend(
                            '<a class="fileuploader-action fileuploader-action-retry" title="Retry"><i></i></a>'
                        ) : null;

                        alert(errorThrown + '. Try again later.');
                    },
                    onProgress: function (data, item, listEl, parentEl, newInputEl, inputEl) {
                        let progressBar = item.html.find('.progress-bar2');

                        if (progressBar.length > 0) {
                            progressBar.show();
                            progressBar.find('span').html(data.percentage + "%");
                            progressBar.find('.fileuploader-progressbar .bar').width(data.percentage + "%");
                        }
                    },
                    onComplete: function (listEl, parentEl, newInputEl, inputEl, jqXHR, textStatus) {
                        // callback will go here
                    }
                }
            }

            $(elm).fileuploader({
                theme: 'simple',
                limit: $(elm).data('limit'),
                fileMaxSize: $(elm).data('file-max-size'),
                extensions: extensions,
                addMore: true,
                upload: upload,
                onRemove: function (item) {
                    // Doesn't have ID, it means file not yet uploaded, no need to delete on server side
                    if (item.data.id === undefined) {
                        return true;
                    }

                    if ($(elm).data('media-url')) {
                        $.post($(elm).data('media-url'), {
                            _token: $(elm).data('token'),
                            _action: 'delete',
                            id: item.data.id
                        });
                    }

                    return true;
                },
                changeInput: '<div class="fileuploader-input">' +
                    '<div class="fileuploader-input-inner">' +
                    '<div><span>${captions.browse}</span></div>' +
                    '</div>' +
                    '</div>',
                captions: {
                    browse: 'Browse or drop files here'
                },
                thumbnails: {
                    removeConfirmation: false
                }
            });
        });

        if (typeof AutoNumeric === 'function' && $('input[data-role="rupiah"]').length > 0) {
            AutoNumeric.multiple('input[data-role="rupiah"]', {
                currencySymbol: '',
                decimalCharacter: ',',
                digitGroupSeparator: '.',
                decimalPlaces: 0,
                unformatOnSubmit: true,
            });
        }

        if (typeof google === 'object' && typeof google.maps === 'object') {
            root.find('[data-form-coordinate]').each(function () {
                let input = $(this);
                let long, lat;
                [lat, long] = input.val().split(',');
                lat = lat || -7.451808;
                long = long || 111.035929;

                let mapContainer = $('<div>')
                    .css('width', '100%')
                    .css('height', 300)
                    .css('border-radius', 4)
                    .css('margin-top', '5px');

                mapContainer.insertAfter($(this));

                let center = new google.maps.LatLng(lat, long);
                let options = {
                    zoom: 17,
                    center: center,
                    mapTypeId: google.maps.MapTypeId.ROADMAP
                }
                let disabled = $(this).is('[disabled]');

                if (disabled) {
                    $.extend(options, {
                        gestureHandling: 'none',
                        zoomControl: false
                    });
                }
                let map = new google.maps.Map(mapContainer[0], options);

                let marker = new google.maps.Marker({
                    position: center,
                    map: map,
                    draggable: !disabled
                });
                google.maps.event.addListener(
                    marker,
                    'drag',
                    function () {
                        input.val(marker.position.lat() + ',' + marker.position.lng());
                    }
                );
            });
        }

        root.find('form[data-ajax]').on('submit', function (e) {
            e.preventDefault();
            let form = $(this);
            $.post(form.attr('action'), form.serialize())
                .done(function () {
                    //TODO on success
                })
                .fail(function (response) {
                    $('body').toast(response.responseJSON.flash);
                    response.responseJSON.fields.forEach((field) => {
                        form.find('[name="'+field+'"]').closest('.field').addClass('error');
                    });
                })
                .always(function () {
                    //TODO enable form
                });
        });
        root.data('initialized', true);
    }
}

const TURBOLINK_ENABLED = $('meta[name="turbolinks-enabled"]').attr('content') === '1';

if (TURBOLINK_ENABLED) {
    let Turbolinks = require("turbolinks")
    Turbolinks.start();

    $(document).on('turbolinks:load', function () {
        Laravolt.init($('body'));
    });

// Keep menu scroll position
    $(document).on('turbolinks:render', function (event) {
        $('#sidebar .simplebar-scroll-content').scrollTop($('#sidebar').data('scroll'));
    });
} else {
    $(document).on('DOMContentLoaded', function () {
        Laravolt.init($('body'));
    });
}

window.addEventListener('laravolt.toast', function (e) {
    $('body').toast(JSON.parse(e.detail));
});

if (typeof Livewire !== "undefined") {
    Livewire.hook('message.processed', (el, component) => {
        const root = $('[wire\\:id="' + component.id + '"]');

        // Make sure Laravolt only initialized once
        if (root.data('initialized') === undefined) {
            Laravolt.init(root);
        }
    })
}
