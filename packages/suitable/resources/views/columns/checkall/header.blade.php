<div class="flex checkbox" data-toggle="checkall" data-selector=".checkbox[data-type='check-all-child']">
    <input type="checkbox"
        class="shrink-0 mt-0.5 border-gray-200 rounded-sm text-blue-600 focus:ring-blue-500 checked:border-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:checked:bg-blue-500 dark:checked:border-blue-500 dark:focus:ring-offset-gray-800">
</div>

@push('script')
    <script>
        Livewire.hook('morphed', ({
            el,
            component
        }) => {
            // reset checkboxes
            const checkboxes = el.querySelectorAll('.checkbox[data-type="check-all-child"] input[type="checkbox"]');
            checkboxes.forEach(checkbox => {
                checkbox.checked = false;
                // Trigger change event for each child
                checkbox.dispatchEvent(new Event('change'));
            });
        });

        // Vanilla JS implementation for check all functionality
        document.querySelectorAll('.checkbox[data-toggle="checkall"]').forEach(function(parentElement) {
            const parentCheckbox = parentElement.querySelector('input[type="checkbox"]');
            const childSelector = parentElement.dataset.selector;
            const childCheckboxes = document.querySelectorAll(childSelector + ' input[type="checkbox"]');

            // Parent checkbox change handler
            parentCheckbox.addEventListener('change', function() {
                const isChecked = this.checked;
                childCheckboxes.forEach(function(childCheckbox) {
                    childCheckbox.checked = isChecked;
                    // Trigger change event for each child
                    childCheckbox.dispatchEvent(new Event('change'));
                });
            });

            // Child checkbox change handler
            function updateParentState() {
                let allChecked = true;
                let allUnchecked = true;
                let ids = [];

                childCheckboxes.forEach(function(childCheckbox) {
                    if (childCheckbox.checked) {
                        allUnchecked = false;
                        // Get the value from the checkbox
                        if (childCheckbox.value) {
                            ids.push(childCheckbox.value);
                        }
                    } else {
                        allChecked = false;
                    }
                });

                // Update parent checkbox value with JSON string of selected IDs
                parentCheckbox.value = JSON.stringify(ids);

                // Set parent checkbox state
                if (allChecked && childCheckboxes.length > 0) {
                    parentCheckbox.checked = true;
                    parentCheckbox.indeterminate = false;
                } else if (allUnchecked) {
                    parentCheckbox.checked = false;
                    parentCheckbox.indeterminate = false;
                } else {
                    parentCheckbox.checked = false;
                    parentCheckbox.indeterminate = true;
                }
            }

            // Attach change event to all child checkboxes
            childCheckboxes.forEach(function(childCheckbox) {
                childCheckbox.addEventListener('change', updateParentState);
            });

            // Initialize parent state on load
            updateParentState();
        });
    </script>
@endpush
