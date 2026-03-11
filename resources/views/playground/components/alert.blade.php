{{-- Alerts & Toasts --}}
<h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-1">Alerts & Toasts</h2>
<p class="text-sm text-gray-500 dark:text-neutral-400 mb-4">Notification patterns with dismiss actions and rich styling.</p>

<div class="space-y-4">
    {{-- Success --}}
    <div class="bg-teal-50 border-t-2 border-teal-500 rounded-lg p-4 dark:bg-teal-800/30" role="alert" tabindex="-1" aria-labelledby="hs-bordered-success-style-label">
        <div class="flex">
            <div class="shrink-0">
                <span class="inline-flex justify-center items-center size-8 rounded-full border-4 border-teal-100 bg-teal-200 text-teal-800 dark:border-teal-900 dark:bg-teal-800 dark:text-teal-400">
                    <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z"/><path d="m9 12 2 2 4-4"/></svg>
                </span>
            </div>
            <div class="ms-3">
                <h3 id="hs-bordered-success-style-label" class="text-gray-800 font-semibold dark:text-white">Successfully updated.</h3>
                <p class="text-sm text-gray-700 dark:text-neutral-400">Your profile changes have been saved and are now live across the platform.</p>
            </div>
        </div>
    </div>

    {{-- Info --}}
    <div class="bg-blue-50 border-s-4 border-blue-500 p-4 dark:bg-blue-800/30" role="alert" tabindex="-1" aria-labelledby="hs-bordered-info">
        <div class="flex">
            <div class="shrink-0">
                <span class="inline-flex justify-center items-center size-8 rounded-full border-4 border-blue-100 bg-blue-200 text-blue-800 dark:border-blue-900 dark:bg-blue-800 dark:text-blue-400">
                    <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>
                </span>
            </div>
            <div class="ms-3">
                <h3 id="hs-bordered-info" class="text-gray-800 font-semibold dark:text-white">Scheduled maintenance</h3>
                <p class="text-sm text-gray-700 dark:text-neutral-400">System maintenance is scheduled for March 15, 2026 at 02:00 WIB. Expected downtime: 30 minutes.</p>
            </div>
        </div>
    </div>

    {{-- Warning --}}
    <div class="bg-yellow-50 border border-yellow-200 text-sm text-yellow-800 rounded-lg p-4 dark:bg-yellow-800/10 dark:border-yellow-900 dark:text-yellow-500" role="alert" tabindex="-1" aria-labelledby="hs-with-description-warning">
        <div class="flex">
            <div class="shrink-0">
                <svg class="shrink-0 size-4 mt-0.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><path d="M12 9v4"/><path d="M12 17h.01"/></svg>
            </div>
            <div class="ms-4">
                <h3 id="hs-with-description-warning" class="font-semibold">Storage limit warning</h3>
                <div class="mt-1 text-sm text-yellow-700 dark:text-yellow-400">You have used 85% of your storage quota (8.5 GB of 10 GB). Consider archiving old files or upgrading your plan.</div>
            </div>
        </div>
    </div>

    {{-- Danger Dismissible --}}
    <div id="dismiss-alert-danger" class="hs-removing:translate-x-5 hs-removing:opacity-0 transition duration-300 bg-red-50 border border-red-200 text-sm text-red-800 rounded-lg p-4 dark:bg-red-800/10 dark:border-red-900 dark:text-red-500" role="alert" tabindex="-1" aria-labelledby="hs-dismiss-danger">
        <div class="flex">
            <div class="shrink-0">
                <svg class="shrink-0 size-4 mt-0.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="m15 9-6 6"/><path d="m9 9 6 6"/></svg>
            </div>
            <div class="ms-4 flex-1">
                <h3 id="hs-dismiss-danger" class="font-semibold">Authentication failed</h3>
                <div class="mt-1 text-sm text-red-700 dark:text-red-400">Invalid credentials. Please check your email and password, then try again.</div>
            </div>
            <div class="ps-3 ms-auto">
                <div class="-mx-1.5 -my-1.5">
                    <button type="button" class="inline-flex bg-red-50 rounded-lg p-1.5 text-red-500 hover:bg-red-100 focus:outline-none focus:bg-red-100 dark:bg-transparent dark:hover:bg-red-800/50 dark:focus:bg-red-800/50" data-hs-remove-element="#dismiss-alert-danger">
                        <span class="sr-only">Dismiss</span>
                        <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
