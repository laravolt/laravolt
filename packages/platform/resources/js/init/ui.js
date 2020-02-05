class Laravolt {
  static init(root) {
    root.find('table.unstackable.responsive').basictable();

    root.find('.ui.checkbox').checkbox();

    root.find('.ui.dropdown:not(.tag)').dropdown({
      forceSelection: false,
      selectOnKeydown: false,
      fullTextSearch: 'exact',
    });

    root.find('.ui.dropdown.tag').each(function () {

      var selected = $(this).data('value').toString().split(',');
      if (selected.length == 1 && selected[0] == '') {
        selected = false;
      }

      $(this).dropdown({
        forceSelection: false,
        allowAdditions: true,
        keys: {
          delimiter: 13
        }
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

    for (var parentName of Object.keys(dependenciesSelect)) {
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

              child.api({
                url: url,
                urlData: {parent: value, payload: payload},
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
                }
              });
            }

          })
        }
      });
    }

    for (var parentName of Object.keys(dependenciesInput)) {
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

            child.api({
              url: url,
              urlData: {parent: value, payload: payload},
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
      var $parent = $(this);
      var $childCheckbox = $(document).find($parent.data('selector'));
      var $storage = $(document).find($parent.data('storage'));

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
      elm = $(elm);

      var type = elm.data('calendar-type');
      if (!type) {
        type = 'date';
      }

      var format = elm.data('calendar-format');
      if (!format) {
        format = 'YYYY-MM-DD';
      }

      elm.calendar({
        type: type,
        ampm: false,
        formatter: {
          date: function (date, settings) {
            if (!date) {
              return '';
            }
            var h = date.getHours();
            var i = date.getMinutes();
            var s = date.getSeconds();
            var j = date.getDate();
            var DD = ("0" + date.getDate()).slice(-2);
            var d = DD;
            var n = date.getMonth() + 1;
            var MM = ("0" + (date.getMonth() + 1)).slice(-2);
            var m = MM;
            var MMMM = settings.text.months[date.getMonth()];
            var M = settings.text.monthsShort[date.getMonth()];
            var YY = date.getFullYear().toString().substr(2, 2);
            var Y = date.getFullYear();
            var YYYY = date.getFullYear();

            return format
              .replace('h', h)
              .replace('i', i)
              .replace('s', s)
              .replace('j', j)
              .replace('d', d)
              .replace('n', n)
              .replace('m', m)
              .replace('M', M)
              .replace('Y', Y)
              .replace('DD', DD)
              .replace('MMMM', MMMM)
              .replace('MM', MM)
              .replace('YYYY', YYYY)
              .replace('YY', YY);
          }
        }
      })
      ;
    });

    root.find('input[type=file].uploader').each(function (idx, elm) {
      var extensions = $(elm).data('extensions');
      if (extensions) {
        extensions = extensions.split(',');
      } else {
        extensions = null;
      }

      var upload = null;

      if ($(elm).data('media-url')) {
        upload = {
          url: $(elm).data('media-url'),
          data: {_token: $(elm).data('token'), _key: $(elm).attr('name'), _action: 'upload'},
          type: 'POST',
          enctype: 'multipart/form-data',
          start: true,
          synchron: true,
          chunk: false,
          onSuccess: function (data, item, listEl, parentEl, newInputEl, inputEl, textStatus, jqXHR) {

            item.local = data.files[0].file;
            item.html.find('.fileuploader-action-remove').addClass('fileuploader-action-success');

            setTimeout(function () {
              item.html.find('.progress-bar2').fadeOut(400);
            }, 400);
          },
          onError: function (item, listEl, parentEl, newInputEl, inputEl, jqXHR, textStatus, errorThrown) {
            var progressBar = item.html.find('.progress-bar2');

            if (progressBar.length > 0) {
              progressBar.find('span').html(0 + "%");
              progressBar.find('.fileuploader-progressbar .bar').width(0 + "%");
              item.html.find('.progress-bar2').fadeOut(400);
            }

            item.upload.status != 'cancelled' && item.html.find('.fileuploader-action-retry').length == 0 ? item.html.find('.column-actions').prepend(
              '<a class="fileuploader-action fileuploader-action-retry" title="Retry"><i></i></a>'
            ) : null;
          },
          onProgress: function (data, item, listEl, parentEl, newInputEl, inputEl) {
            var progressBar = item.html.find('.progress-bar2');

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
        extensions: extensions,
        addMore: true,
        upload: upload,
        onRemove: function (item) {
          if ($(elm).data('media-url')) {
            $.post($(elm).data('media-url'), {
              _token: $(elm).data('token'),
              _action: 'delete',
              file: item.file,
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

    if (typeof AutoNumeric === 'function') {
      AutoNumeric.multiple('input[data-role="rupiah"]', {
        currencySymbol: '',
        decimalCharacter: ',',
        digitGroupSeparator: '.',
        decimalPlaces: 0,
        unformatOnSubmit: true,
      });
    }

    if (jQuery().redactor) {
      root.find('[data-role="redactor"]').each(function () {
        $(this).redactor({
          plugins: [
            'fontcolor',
            'alignment',
            'video',
            'fullscreen',
            'table',
          ],
          toolbarFixedTopOffset: 50,
          minHeight: '300px',
          imageUpload: $(this).data('upload-url'),
          imageResizable: true,
          imagePosition: true,
          imageData: {
            '_token': $(this).data('token'),
          },
          fontcolors: [
            '#000000',
            '#111111',
            '#222222',
            '#333333',
            '#666666',
            '#999999',
            '#BBBBBB',
            '#CCCCCC',
            '#DDDDDD',
            '#EEEEEE',
            '#FFFFFF',
            '#f44336',
            '#f44336',
            '#E91E63',
            '#9C27B0',
            '#673AB7',
            '#3F51B5',
            '#2196F3',
            '#03A9F4',
            '#00BCD4',
            '#009688',
            '#4CAF50',
            '#8BC34A',
            '#CDDC39',
            '#FFC107',
            '#FF9800',
            '#FF5722',
            '#FF0000',
            '#b026fe',
            '#0000ff',
            '#00FF00',
            '#fff000',
            '#ff6000',
          ],
        });
      });
    }


    if (typeof google === 'object' && typeof google.maps === 'object') {
      root.find('[data-form-coordinate]').each(function () {
        var input = $(this);
        var long, lat;
        [lat, long] = input.val().split(',');
        lat = lat || -7.451808;
        long = long || 111.035929;

        var mapContainer = $('<div>')
          .css('width', '100%')
          .css('height', 300)
          .css('border-radius', 4)
          .css('margin-top', '5px');

        mapContainer.insertAfter($(this));

        var center = new google.maps.LatLng(lat, long);
        var options = {
          zoom: 17,
          center: center,
          mapTypeId: google.maps.MapTypeId.ROADMAP
        }
        var disabled = $(this).is('[disabled]');

        if (disabled) {
          $.extend(options, {
            gestureHandling: 'none',
            zoomControl: false
          });
        }
        var map = new google.maps.Map(mapContainer[0], options);

        var marker = new google.maps.Marker({
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
  }
}

$(function () {
  Laravolt.init($('body'));
  $('[data-page-loader]').removeClass('active');
});


