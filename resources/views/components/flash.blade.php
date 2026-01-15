@if (!empty($bags))
    <script>
        function tostifyCustomClose(el) {
            const parent = el.closest('.toastify');
            const close = parent.querySelector('.toast-close');

            if (close) {
                close.click();
            }
        }

        function createToast(bag) {
            console.log(bag)
            let toastMarkup = '';

            // Determine toast type and styling based on bag properties
            const isError = bag.showIcon && bag.showIcon.includes('red') || bag.classProgress === 'red';
            const isSuccess = bag.showIcon && bag.showIcon.includes('green') || bag.classProgress === 'green';
            const isWarning = bag.showIcon && bag.showIcon.includes('orange') || bag.classProgress === 'orange';
            const isInfo = bag.showIcon && bag.showIcon.includes('blue') || bag.classProgress === 'blue';

            // Create icon based on type
            let icon = '';
            let bgColor = 'bg-white dark:bg-neutral-800';
            let textColor = 'text-gray-800 dark:text-white';
            let iconColor = 'text-gray-500 dark:text-neutral-400';

            if (isError) {
                icon =
                    '<svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>';
                iconColor = 'text-red-500';
            } else if (isSuccess) {
                icon =
                    '<svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22,4 12,14.01 9,11.01"></polyline></svg>';
                iconColor = 'text-green-500';
            } else if (isWarning) {
                icon =
                    '<svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>';
                iconColor = 'text-yellow-500';
            } else {
                icon =
                    '<svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>';
                iconColor = 'text-blue-500';
            }

            toastMarkup = `
                <div class="max-w-xs relative bg-white border border-gray-200 rounded-xl shadow-lg dark:bg-neutral-800 dark:border-neutral-700" role="alert" tabindex="-1">
                    <div class="flex items-start gap-3 p-4">
                        <div class="flex-shrink-0">
                            <div class="inline-flex justify-center items-center w-8 h-8 rounded-full ${iconColor} bg-gray-50 dark:bg-neutral-700">
                                ${icon}
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="text-sm font-medium ${textColor} dark:text-white">
                                ${bag.message}
                            </div>
                        </div>
                        <button onclick="tostifyCustomClose(this)" type="button" class="flex-shrink-0 inline-flex justify-center items-center w-5 h-5 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors duration-200 focus:outline-hidden focus:ring-2 focus:ring-gray-500 dark:text-neutral-400 dark:hover:text-neutral-200 dark:hover:bg-neutral-700" aria-label="Close">
                            <span class="sr-only">Close</span>
                            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M18 6 6 18"></path>
                                <path d="m6 6 12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            `;

            return toastMarkup;
        }

        // Check if Toastify is available, if not, use a fallback
        if (typeof Toastify !== 'undefined') {
            @foreach ($bags as $bag)
                Toastify({
                    text: createToast({!! json_encode($bag) !!}),
                    className: "hs-toastify-on:opacity-100 opacity-0 fixed bottom-5 end-5 z-[100] transition-all duration-300 w-72 [&>.toast-close]:hidden p-0",
                    duration: {{ $bag['displayTime'] === 'auto' ? $bag['minDisplayTime'] ?? 3000 : $bag['displayTime'] }},
                    close: true,
                    escapeMarkup: false,
                    gravity: "bottom",
                    position: "right"
                }).showToast();
            @endforeach
        } else {
            // Fallback: Create toast manually without Toastify
            @foreach ($bags as $bag)
                (function() {
                    const toastContainer = document.createElement('div');
                    toastContainer.className = 'fixed bottom-5 end-5 z-[100] transition-all duration-300 w-72';
                    toastContainer.innerHTML = createToast({!! json_encode($bag) !!});

                    document.body.appendChild(toastContainer);

                    // Auto remove after duration
                    setTimeout(() => {
                            toastContainer.style.opacity = '0';
                            setTimeout(() => {
                                if (toastContainer.parentNode) {
                                    toastContainer.parentNode.removeChild(toastContainer);
                                }
                            }, 300);
                        },
                        {{ $bag['displayTime'] === 'auto' ? $bag['minDisplayTime'] ?? 3000 : $bag['displayTime'] }}
                    );

                    // Add click to close functionality
                    const closeBtn = toastContainer.querySelector('[aria-label="Close"]');
                    if (closeBtn) {
                        closeBtn.addEventListener('click', () => {
                            toastContainer.style.opacity = '0';
                            setTimeout(() => {
                                if (toastContainer.parentNode) {
                                    toastContainer.parentNode.removeChild(toastContainer);
                                }
                            }, 300);
                        });
                    }
                })();
            @endforeach
        }
    </script>
@endif
