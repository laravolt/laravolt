<x-volt-base :title="$title">
    <!-- ========== MAIN CONTENT ========== -->
    <div class="relative h-full">
        
        <!-- ========== SIDEBAR ========== -->
        @include('laravolt::menu.sidebar_modern')
        
        <!-- ========== CONTENT ========== -->
        <div class="w-full lg:ps-64">
            
            <!-- ========== HEADER ========== -->
            @include('laravolt::menu.topbar_modern')
            
            <!-- ========== MAIN ========== -->
            <main id="content" 
                  class="relative z-10 bg-white dark:bg-slate-900"
                  up-main="root"
                  data-font-size="{{ config('laravolt.ui.font_size') }}"
                  data-theme="{{ config('laravolt.ui.theme') }}"
                  data-accent-color="{{ config('laravolt.ui.color') }}"
                  data-sidebar-density="{{ config('laravolt.ui.sidebar_density') }}"
            >
                <!-- Action Bar (Breadcrumbs, etc) -->
                @include('laravolt::menu.actionbar_modern')
                
                <!-- Page Content -->
                <div class="p-4 sm:p-6 space-y-4 sm:space-y-6"
                     up-main="modal"
                >
                    {{ $slot }}
                    @stack('main')
                </div>
                
            </main>
            <!-- ========== END MAIN ========== -->
            
        </div>
        <!-- ========== END CONTENT ========== -->
        
    </div>
    <!-- ========== END MAIN CONTENT ========== -->
</x-volt-base>
