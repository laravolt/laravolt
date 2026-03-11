{{-- Stepper — Preline hs-stepper --}}
<h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-1">Stepper</h2>
<p class="text-sm text-gray-500 dark:text-neutral-400 mb-4">Multi-step wizard powered by <code class="text-xs bg-gray-100 dark:bg-neutral-700 px-1.5 py-0.5 rounded">hs-stepper</code> plugin with progress tracking.</p>

<x-volt-panel title="Registration Wizard">
    <div data-hs-stepper='{"currentIndex": 1}'>
        {{-- Stepper Nav --}}
        <ul class="relative flex flex-row gap-x-2 mb-8">
            <li class="flex items-center gap-x-2 shrink basis-0 flex-1 group" data-hs-stepper-nav-item='{"index": 1}'>
                <span class="min-w-7 min-h-7 group inline-flex items-center text-xs align-middle">
                    <span class="size-7 flex justify-center items-center shrink-0 bg-blue-600 font-medium text-white rounded-full group-focus:bg-blue-600 hs-stepper-active:bg-blue-600 hs-stepper-success:bg-blue-600 hs-stepper-completed:bg-green-500 dark:bg-blue-500 dark:hs-stepper-active:bg-blue-500">
                        <span class="hs-stepper-success:hidden hs-stepper-completed:hidden">1</span>
                        <svg class="hidden shrink-0 size-3 hs-stepper-success:block" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    </span>
                    <span class="ms-2 text-sm font-medium text-gray-800 dark:text-white">Account</span>
                </span>
                <div class="w-full h-px flex-1 bg-gray-200 group-last:hidden hs-stepper-success:bg-blue-600 hs-stepper-completed:bg-green-500 dark:bg-neutral-700"></div>
            </li>
            <li class="flex items-center gap-x-2 shrink basis-0 flex-1 group" data-hs-stepper-nav-item='{"index": 2}'>
                <span class="min-w-7 min-h-7 group inline-flex items-center text-xs align-middle">
                    <span class="size-7 flex justify-center items-center shrink-0 bg-gray-100 font-medium text-gray-800 rounded-full group-focus:bg-gray-200 hs-stepper-active:bg-blue-600 hs-stepper-active:text-white hs-stepper-success:bg-blue-600 hs-stepper-success:text-white hs-stepper-completed:bg-green-500 hs-stepper-completed:text-white dark:bg-neutral-700 dark:text-white dark:hs-stepper-active:bg-blue-500">
                        <span class="hs-stepper-success:hidden hs-stepper-completed:hidden">2</span>
                        <svg class="hidden shrink-0 size-3 hs-stepper-success:block" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    </span>
                    <span class="ms-2 text-sm font-medium text-gray-800 dark:text-white">Profile</span>
                </span>
                <div class="w-full h-px flex-1 bg-gray-200 group-last:hidden hs-stepper-success:bg-blue-600 hs-stepper-completed:bg-green-500 dark:bg-neutral-700"></div>
            </li>
            <li class="flex items-center gap-x-2 shrink basis-0 flex-1 group" data-hs-stepper-nav-item='{"index": 3}'>
                <span class="min-w-7 min-h-7 group inline-flex items-center text-xs align-middle">
                    <span class="size-7 flex justify-center items-center shrink-0 bg-gray-100 font-medium text-gray-800 rounded-full group-focus:bg-gray-200 hs-stepper-active:bg-blue-600 hs-stepper-active:text-white hs-stepper-success:bg-blue-600 hs-stepper-success:text-white hs-stepper-completed:bg-green-500 hs-stepper-completed:text-white dark:bg-neutral-700 dark:text-white dark:hs-stepper-active:bg-blue-500">
                        <span class="hs-stepper-success:hidden hs-stepper-completed:hidden">3</span>
                        <svg class="hidden shrink-0 size-3 hs-stepper-success:block" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    </span>
                    <span class="ms-2 text-sm font-medium text-gray-800 dark:text-white">Review</span>
                </span>
                <div class="w-full h-px flex-1 bg-gray-200 group-last:hidden hs-stepper-success:bg-blue-600 hs-stepper-completed:bg-green-500 dark:bg-neutral-700"></div>
            </li>
        </ul>

        {{-- Stepper Content --}}
        <div class="mt-5 sm:mt-8">
            {{-- Step 1 --}}
            <div data-hs-stepper-content-item='{"index": 1}'>
                <div class="p-4 bg-gray-50 border border-dashed border-gray-200 rounded-xl dark:bg-neutral-800 dark:border-neutral-700">
                    <h4 class="text-lg font-semibold text-gray-800 dark:text-white mb-3">Account Information</h4>
                    <div class="grid sm:grid-cols-2 gap-4">
                        <div><label class="block text-sm font-medium mb-2 dark:text-white">Email</label><input type="email" class="py-2.5 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400" placeholder="you@example.com"></div>
                        <div><label class="block text-sm font-medium mb-2 dark:text-white">Password</label><input type="password" class="py-2.5 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400" placeholder="••••••••"></div>
                    </div>
                </div>
            </div>

            {{-- Step 2 --}}
            <div data-hs-stepper-content-item='{"index": 2}' style="display: none;">
                <div class="p-4 bg-gray-50 border border-dashed border-gray-200 rounded-xl dark:bg-neutral-800 dark:border-neutral-700">
                    <h4 class="text-lg font-semibold text-gray-800 dark:text-white mb-3">Profile Details</h4>
                    <div class="grid sm:grid-cols-2 gap-4">
                        <div><label class="block text-sm font-medium mb-2 dark:text-white">Full Name</label><input type="text" class="py-2.5 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400" placeholder="John Doe"></div>
                        <div><label class="block text-sm font-medium mb-2 dark:text-white">Role</label>
                            <select class="py-2.5 px-4 pe-9 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400">
                                <option>Developer</option><option>Designer</option><option>Manager</option><option>Admin</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Step 3 --}}
            <div data-hs-stepper-content-item='{"index": 3}' style="display: none;">
                <div class="p-4 bg-gray-50 border border-dashed border-gray-200 rounded-xl dark:bg-neutral-800 dark:border-neutral-700">
                    <h4 class="text-lg font-semibold text-gray-800 dark:text-white mb-3">Review & Confirm</h4>
                    <p class="text-gray-600 dark:text-neutral-400">Please review your information before submitting. All details can be changed later from your account settings.</p>
                    <div class="mt-3 flex items-center">
                        <input type="checkbox" class="shrink-0 border-gray-200 rounded text-blue-600 focus:ring-blue-500 dark:bg-neutral-800 dark:border-neutral-700 dark:checked:bg-blue-500 dark:checked:border-blue-500 dark:focus:ring-offset-gray-800" id="stepper-agree">
                        <label for="stepper-agree" class="text-sm text-gray-500 ms-3 dark:text-neutral-400">I agree to the <a class="text-blue-600 decoration-2 hover:underline dark:text-blue-500" href="#">Terms and Conditions</a></label>
                    </div>
                </div>
            </div>

            {{-- Final --}}
            <div data-hs-stepper-content-item='{"isFinal": true}' style="display: none;">
                <div class="p-4 bg-green-50 border border-green-200 rounded-xl text-center dark:bg-green-900/20 dark:border-green-800">
                    <svg class="mx-auto size-12 text-green-500 mb-3" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    <h4 class="text-lg font-semibold text-green-800 dark:text-green-400">Registration Complete!</h4>
                    <p class="mt-1 text-sm text-green-600 dark:text-green-300">Your account has been created successfully.</p>
                </div>
            </div>
        </div>

        {{-- Stepper Actions --}}
        <div class="mt-5 flex justify-between items-center gap-x-2">
            <button type="button" class="py-2 px-3 inline-flex items-center gap-x-1 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:text-neutral-300 dark:hover:bg-neutral-700" data-hs-stepper-back-btn="">
                <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
                Back
            </button>
            <button type="button" class="py-2 px-3 inline-flex items-center gap-x-1 text-sm font-medium rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none dark:bg-blue-500 dark:hover:bg-blue-600" data-hs-stepper-next-btn="">
                Next
                <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
            </button>
            <button type="button" class="py-2 px-3 inline-flex items-center gap-x-1 text-sm font-medium rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none" data-hs-stepper-finish-btn="" style="display: none;">
                Finish
            </button>
            <button type="reset" class="py-2 px-3 inline-flex items-center gap-x-1 text-sm font-medium rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none" data-hs-stepper-reset-btn="" style="display: none;">
                Reset
            </button>
        </div>
    </div>
</x-volt-panel>
