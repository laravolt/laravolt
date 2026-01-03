<tr>
    <td colspan="{{ count($columns) }}" class="p-5 text-center">
        <!-- Empty State -->
        <div class="flex flex-col justify-center items-center text-center">
            <svg class="w-20 h-20 mx-auto mb-4 text-gray-400 dark:text-neutral-600" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round">
                <rect width="18" height="18" x="3" y="3" rx="2" ry="2"/>
                <path d="M3 9h18"/>
                <path d="M9 21V9"/>
            </svg>
            <div class="max-w-sm mx-auto">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-neutral-200">
                    {{ __('No results found') }}
                </h3>
                <p class="text-sm text-gray-600 dark:text-neutral-400 mt-2">
                    {{ __('Try adjusting your search or filter to find what you\'re looking for.') }}
                </p>
            </div>
        </div>
        <!-- End Empty State -->
    </td>
</tr>
