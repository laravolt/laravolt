<tr>
    <td colspan="{{ count($columns) }}" class="p-5 text-center">
        <!-- Empty State -->
        <div class="flex flex-col justify-center items-center text-center">
            <x-volt-icon name="empty-set" class="w-20 h-20 mx-auto mb-4 text-gray-400 dark:text-neutral-600" />
            <div class="max-w-sm mx-auto">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-neutral-200">
                    @lang('suitable::suitable.empty_message')
                </h3>
                <p class="text-sm text-gray-600 dark:text-neutral-400 mt-2">
                    {{ __('Try adjusting your search or filter to find what you\'re looking for.') }}
                </p>
            </div>
        </div>
        <!-- End Empty State -->
    </td>
</tr>
