@if ($actions->isNotEmpty())
    <div class="flex items-center gap-x-1">
        @if ($actions->has('show'))
            <a href="{{ $actions->get('show') }}" up-layer="new" up-mode="modal"
                class="inline-flex justify-center items-center size-8 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-500 hover:bg-gray-50 hover:text-gray-700 focus:outline-hidden focus:bg-gray-50 focus:text-gray-700 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:text-neutral-400 dark:hover:bg-neutral-700 dark:hover:text-neutral-300 dark:focus:bg-neutral-700 dark:focus:text-neutral-300"
                title="View">
                {!! svg(config('laravolt.ui.iconset') . '-eye', null, [
                    'class' => 'shrink-0 mt-0.5 size-4 dark:fill-white',
                    'fill' => 'currentColor',
                ])->toHtml() !!}
            </a>
        @endif

        @if ($actions->has('edit'))
            <a href="{{ $actions->get('edit') }}" up-layer="new" up-mode="modal"
                class="inline-flex justify-center items-center size-8 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-500 hover:bg-gray-50 hover:text-gray-700 focus:outline-hidden focus:bg-gray-50 focus:text-gray-700 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:text-neutral-400 dark:hover:bg-neutral-700 dark:hover:text-neutral-300 dark:focus:bg-neutral-700 dark:focus:text-neutral-300"
                title="Edit">
                {!! svg(config('laravolt.ui.iconset') . '-pencil', null, [
                    'class' => 'shrink-0 mt-0.5 size-4 dark:fill-white',
                    'fill' => 'currentColor',
                ])->toHtml() !!}
            </a>
        @endif

        @if ($actions->has('destroy'))
            <button type="button"
                class="inline-flex justify-center items-center size-8 text-sm font-medium rounded-lg border border-red-200 bg-white text-red-500 hover:bg-red-50 hover:text-red-700 hover:border-red-300 focus:outline-hidden focus:bg-red-50 focus:text-red-700 focus:border-red-300 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-red-600/50 dark:text-red-400 dark:hover:bg-red-600/10 dark:hover:text-red-300 dark:hover:border-red-600 dark:focus:bg-red-600/10 dark:focus:text-red-300 dark:focus:border-red-600"
                title="Delete" data-modal-target="delete-modal-{{ md5($key) }}" onclick="showDeleteModal(this)">
                {!! svg(config('laravolt.ui.iconset') . '-trash', null, [
                    'class' => 'shrink-0 mt-0.5 size-4',
                    'fill' => 'currentColor',
                ])->toHtml() !!}
            </button>
        @endif
    </div>

    @if ($actions->has('destroy'))
        <form id="{{ $key }}" role="form" action="{{ $actions->get('destroy') }}" method="POST">
            <input type="hidden" name="_method" value="DELETE">
            {{ csrf_field() }}
        </form>

        <!-- Delete Confirmation Modal -->
        <div id="delete-modal-{{ md5($key) }}" class="hidden fixed inset-0 z-80 overflow-x-hidden overflow-y-auto">
            <div class="flex justify-center pt-10 p-4">
                <div class="fixed inset-0 bg-black/40" onclick="hideDeleteModal('delete-modal-{{ md5($key) }}')">
                </div>
                <div
                    class="relative w-full max-w-lg bg-white border border-gray-200 shadow-2xs rounded-xl dark:bg-neutral-800 dark:border-neutral-700 dark:shadow-neutral-700/70">
                    <div
                        class="flex justify-between items-center py-3 px-4 border-b border-gray-200 dark:border-neutral-700">
                        <h3 class="font-bold text-gray-800 dark:text-white">
                            Confirm Delete
                        </h3>
                        <button type="button"
                            class="size-8 inline-flex justify-center items-center gap-x-2 rounded-full border border-transparent bg-gray-100 text-gray-800 hover:bg-gray-200 focus:outline-hidden focus:bg-gray-200 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-700 dark:hover:bg-neutral-600 dark:text-neutral-400 dark:focus:bg-neutral-600"
                            aria-label="Close" onclick="hideDeleteModal('delete-modal-{{ md5($key) }}')">
                            <span class="sr-only">Close</span>
                            <x-volt-icon name="times" size="16" />
                        </button>
                    </div>
                    <div class="p-4 overflow-y-auto">
                        <p class="mt-1 text-gray-800 dark:text-neutral-400">
                            {{ $deleteConfirmation ?? 'Are you sure you want to delete this item? This action cannot be undone.' }}
                        </p>
                    </div>
                    <div
                        class="flex justify-end items-center gap-x-2 py-3 px-4 border-t border-gray-200 dark:border-neutral-700">
                        <button type="button"
                            class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-2xs hover:bg-gray-50 focus:outline-hidden focus:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:text-white dark:hover:bg-neutral-700 dark:focus:bg-neutral-700"
                            onclick="hideDeleteModal('delete-modal-{{ md5($key) }}')" autofocus>
                            Cancel
                        </button>
                        <button type="submit" form="{{ $key }}"
                            class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-red-600 text-white hover:bg-red-700 focus:outline-hidden focus:bg-red-700 disabled:opacity-50 disabled:pointer-events-none">
                            Delete
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <script>
            function showDeleteModal(button) {
                const modalId = button.getAttribute('data-modal-target');
                const modal = document.getElementById(modalId);
                if (modal) {
                    modal.classList.remove('hidden');
                    // Focus the Cancel button after modal opens
                    setTimeout(() => {
                        const cancelButton = modal.querySelector('button[autofocus]');
                        if (cancelButton) {
                            cancelButton.focus();
                        }
                    }, 100);
                } else {
                    console.error('Modal not found:', modalId);
                }
            }

            function hideDeleteModal(modalId) {
                const modal = document.getElementById(modalId);
                if (modal) {
                    modal.classList.add('hidden');
                } else {
                    console.error('Modal not found:', modalId);
                }
            }
        </script>
    @endif
@endif
