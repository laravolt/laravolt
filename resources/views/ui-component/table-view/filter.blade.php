@if ($this->filters())
    <!-- Filter Dropdown -->
    <div class="hs-dropdown [--auto-close:true] relative inline-flex" data-role="suitable-filter">
        <!-- Filter Button -->
        <button type="button"
            class="py-1.5 sm:py-2 px-2.5 inline-flex items-center gap-x-1.5 text-sm sm:text-xs font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-2xs hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none focus:outline-hidden focus:bg-gray-50 dark:bg-neutral-800 dark:border-neutral-700 dark:text-neutral-300 dark:hover:bg-neutral-700 dark:focus:bg-neutral-700"
            data-role="suitable-filter-icon" aria-haspopup="menu" aria-expanded="false" aria-label="Filter">
            <x-volt-icon name="filter" />
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
                            <x-volt-icon name="times" />
                            Clear All
                        </button>
                    </div>

                    <div class="border-t border-gray-200 dark:border-neutral-700"></div>

                    <form class="space-y-4" wire:submit.prevent>
                        @foreach ($this->filters() as $filter)
                            <div class="space-y-2">
                                {!! $filter->render() !!}
                            </div>
                        @endforeach
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
