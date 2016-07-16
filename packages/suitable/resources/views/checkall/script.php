<script>
    $(function () {
        $('.checkbox[data-toggle="checkall"]').each(function () {
            var $parent = $(this);
            var $childCheckbox = $(document).find($parent.data('selector'));

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
                            }
                            else {
                                allChecked = false;
                            }
                        });

                        // change multiple delete form action, based on selected ids
                        var form = $('form[data-type="delete-multiple"]');
                        if (form.length > 0) {
                            var url = $('form[data-type="delete-multiple"]').attr('action');
                            var replaceStartFrom = url.lastIndexOf('/');
                            var newUrl = url.substr(0, replaceStartFrom) + '/' + ids.join(',');
                            $('form[data-type="delete-multiple"]').attr('action', newUrl);
                        }

                        // set parent checkbox state, but dont trigger its onChange callback
                        if (allChecked) {
                            $parentCheckbox.checkbox('set checked');
                            form.find('[type="submit"]').removeClass('disabled');
                            //form.css('visibility', 'visible');
                        }
                        else if (allUnchecked) {
                            $parentCheckbox.checkbox('set unchecked');
                            form.find('[type="submit"]').addClass('disabled');
                            //form.css('visibility', 'hidden');
                        }
                        else {
                            $parentCheckbox.checkbox('set indeterminate');
                            form.find('[type="submit"]').removeClass('disabled');
                            //form.css('visibility', 'visible');
                        }
                    }
                })
            ;
        });
    });
</script>
