@if ($this->filters())
    <!-- Filter Dropdown -->
    <div class="hs-dropdown [--auto-close:false] relative inline-flex" data-role="suitable-filter">
        <!-- Filter Button -->
        <button type="button"
            class="py-1.5 sm:py-2 px-2.5 inline-flex items-center gap-x-1.5 text-sm sm:text-xs font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-2xs hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none focus:outline-hidden focus:bg-gray-50 dark:bg-neutral-800 dark:border-neutral-700 dark:text-neutral-300 dark:hover:bg-neutral-700 dark:focus:bg-neutral-700"
            data-role="suitable-filter-icon" aria-haspopup="menu" aria-expanded="false" aria-label="Filter">
            {!! svg(config('laravolt.ui.iconset') . '-' . 'filter', null, [
                'class' => 'shrink-0 mt-0.5 size-4 dark:fill-white',
                'fill' => 'currentColor',
            ])->toHtml() !!}
            Filter
        </button>
        <!-- End Filter Button -->

        <!-- Dropdown -->
        <div class="hs-dropdown-menu hs-dropdown-open:opacity-100 w-80 transition-[opacity,margin] duration opacity-0 hidden z-10 bg-white rounded-xl shadow-xl dark:bg-neutral-900"
            role="menu" wire:ignore.self>
            <div class="p-4">
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <h4 class="text-sm font-semibold text-gray-800 dark:text-neutral-200">
                            Filters
                        </h4>
                        <button type="button"
                            class="py-1.5 px-3 inline-flex items-center gap-x-1.5 text-xs font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-2xs hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none focus:outline-hidden focus:bg-gray-50 dark:bg-neutral-800 dark:border-neutral-700 dark:text-neutral-300 dark:hover:bg-neutral-700 dark:focus:bg-neutral-700"
                            wire:click="resetFilters">
                            {!! svg(config('laravolt.ui.iconset') . '-' . 'times', null, [
                                'class' => 'shrink-0 mt-0.5 size-4 dark:fill-white',
                                'fill' => 'currentColor',
                            ])->toHtml() !!}
                            Clear All
                        </button>
                    </div>

                    <div class="border-t border-gray-200 dark:border-neutral-700"></div>

                    <form class="space-y-4" wire:submit="applyFilters">
                        @foreach ($this->filters() as $filter)
                            <div class="space-y-2">
                                {!! $filter->render() !!}
                            </div>
                        @endforeach
                        <button type="submit"
                            class="w-full py-2 px-3 inline-flex items-center justify-center gap-x-1.5 text-sm font-medium rounded-lg border border-transparent bg-blue-600 text-white shadow-sm hover:bg-blue-700 focus:outline-hidden focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:pointer-events-none">
                            Apply Filters
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <!-- End Dropdown -->
    </div>
    <!-- End Filter Dropdown -->

    @once
        @push('scripts')
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    // Initialize Preline dropdowns if available
                    if (window.HSDropdown) {
                        window.HSDropdown.autoInit();
                    }

                    // Fallback for basic dropdown functionality
                    const filterButtons = document.querySelectorAll('[data-role="suitable-filter-icon"]');
                    filterButtons.forEach(button => {
                        button.addEventListener('click', function() {
                            const dropdown = this.nextElementSibling;
                            if (dropdown) {
                                dropdown.classList.toggle('hidden');
                                dropdown.classList.toggle('opacity-0');
                            }
                        });
                    });

                    // Close dropdown when clicking outside
                    document.addEventListener('click', function(event) {
                        const filterDropdowns = document.querySelectorAll('[data-role="suitable-filter"]');
                        filterDropdowns.forEach(filterDropdown => {
                            const button = filterDropdown.querySelector(
                                '[data-role="suitable-filter-icon"]');
                            const dropdown = filterDropdown.querySelector('.hs-dropdown-menu');

                            if (dropdown && !dropdown.classList.contains('hidden')) {
                                // Check if click is outside the entire filter component
                                if (!filterDropdown.contains(event.target)) {
                                    dropdown.classList.add('hidden');
                                    dropdown.classList.add('opacity-0');
                                }
                            }
                        });
                    });

                    // Reset filter functionality
                    const resetButtons = document.querySelectorAll(
                        '[data-role="suitable-filter"] button[wire\\:click="resetFilters"]');
                    resetButtons.forEach(button => {
                        button.addEventListener('click', function() {
                            const form = this.closest('[data-role="suitable-filter"]').querySelector(
                                'form');
                            if (form) {
                                // Reset form inputs
                                const inputs = form.querySelectorAll('input, select, textarea');
                                inputs.forEach(input => {
                                    if (input.type === 'checkbox' || input.type === 'radio') {
                                        input.checked = false;
                                    } else {
                                        input.value = '';
                                    }
                                });
                            }
                        });
                    });
                });
            </script>
        @endpush
    @endonce
@endif
