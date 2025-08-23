@if(View::hasSection('actionbar') || View::hasSection('breadcrumb') || View::hasSection('page-actions'))
<!-- Action Bar -->
<div class="bg-white border-b border-gray-200 px-4 py-3 sm:px-6 dark:bg-slate-900 dark:border-gray-700">
    <div class="flex items-center justify-between">
        
        <!-- Left Side - Breadcrumbs -->
        <div class="min-w-0 flex-1">
            @if(View::hasSection('breadcrumb'))
                <nav class="flex" aria-label="Breadcrumb">
                    <ol class="flex items-center space-x-2">
                        @yield('breadcrumb')
                    </ol>
                </nav>
            @endif
            
            @yield('actionbar')
        </div>
        
        <!-- Right Side - Actions -->
        @if(View::hasSection('page-actions'))
        <div class="flex items-center space-x-3">
            @yield('page-actions')
        </div>
        @endif
        
    </div>
</div>
@endif