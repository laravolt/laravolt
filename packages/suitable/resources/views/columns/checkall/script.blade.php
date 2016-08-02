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

                        // set parent checkbox state, but dont trigger its onChange callback
                        if (allChecked) {
                            $parentCheckbox.checkbox('set checked');
                        }
                        else if (allUnchecked) {
                            $parentCheckbox.checkbox('set unchecked');
                        }
                        else {
                            $parentCheckbox.checkbox('set indeterminate');
                        }
                        $('#{{ $id }}').trigger('suitable.checkall.change', [ids]);
                    }
                })
            ;
        });
    });
</script>
