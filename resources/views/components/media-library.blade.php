<div class="overflow-x-auto">
    <table class="w-full divide-y divide-gray-200 dark:divide-neutral-700">
        <thead class="bg-gray-50 dark:bg-neutral-800">
            <tr>
                <th scope="col" class="px-4 py-3 text-start text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-neutral-400">File</th>
                <th scope="col" class="px-4 py-3 text-start text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-neutral-400">Type</th>
                <th scope="col" class="px-4 py-3 text-start text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-neutral-400">Size</th>
                <th scope="col" class="px-4 py-3 text-end text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-neutral-400"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-neutral-700">
            @foreach($collection as $media)
                <tr class="hover:bg-gray-50 dark:hover:bg-neutral-800 transition-colors">
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-800 dark:text-neutral-200">
                        {{ $media->file_name }}
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap">
                        <x-volt-badge variant="{{ $convertExtensionToColor($media->extension) }}">{{ $media->extension }}</x-volt-badge>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-neutral-400">
                        {{ $media->human_readable_size }}
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-end">
                        <a href="{{ $media->getUrl() }}" target="_blank"
                           class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-sm font-medium">
                            Download
                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
