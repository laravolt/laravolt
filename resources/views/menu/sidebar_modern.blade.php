@php
/** @var \Laravolt\Platform\Services\SidebarMenu */
$items = app('laravolt.menu.sidebar')->allMenu();
@endphp

<!-- ========== SIDEBAR ========== -->
<div id="hs-application-sidebar" 
     class="hs-overlay [--auto-close:lg] hs-overlay-open:translate-x-0 -translate-x-full transition-all duration-300 transform w-[260px] h-full hidden fixed inset-y-0 start-0 z-[60] bg-white border-e border-gray-200 lg:block lg:translate-x-0 lg:end-auto lg:bottom-0 dark:bg-slate-900 dark:border-gray-700"
     role="dialog" 
     tabindex="-1" 
     aria-label="Sidebar">
     
    <div class="relative flex flex-col h-full max-h-full">
        
        <!-- ========== HEADER ========== -->
        <div class="px-6 pt-4">
            @include('laravolt::menu.sidebar_logo_modern')
        </div>
        <!-- ========== END HEADER ========== -->
        
        <!-- ========== CONTENT ========== -->
        <div class="h-full overflow-y-auto [&::-webkit-scrollbar]:w-2 [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-track]:bg-gray-100 [&::-webkit-scrollbar-thumb]:bg-gray-300 dark:[&::-webkit-scrollbar-track]:bg-slate-700 dark:[&::-webkit-scrollbar-thumb]:bg-slate-500">
            
            <!-- Profile Section -->
            @include('laravolt::menu.sidebar_profile_modern')
            
            <!-- Navigation -->
            <nav class="hs-accordion-group p-3 w-full flex flex-col flex-wrap" data-hs-accordion-always-open>
                <ul class="flex flex-col space-y-1">
                    
                    @foreach($items as $menu)
                        @if($menu->hasChildren())
                            <li class="hs-accordion" id="projects-accordion">
                                <button type="button" 
                                        class="hs-accordion-toggle w-full text-start flex items-center gap-x-3.5 py-2 px-2.5 text-sm text-gray-800 rounded-lg hover:bg-gray-100 focus:outline-none focus:bg-gray-100 dark:bg-slate-900 dark:text-white dark:hover:bg-slate-800 dark:focus:bg-slate-800"
                                        aria-expanded="false" 
                                        aria-controls="projects-accordion-child">
                                    @if($menu->data('icon'))
                                        <x-icon name="{{ $menu->data('icon') }}" class="shrink-0 size-4" />
                                    @endif
                                    {{ $menu->title }}
                                    
                                    <svg class="hs-accordion-active:rotate-180 ms-auto shrink-0 size-4" 
                                         xmlns="http://www.w3.org/2000/svg" 
                                         width="24" 
                                         height="24" 
                                         viewBox="0 0 24 24" 
                                         fill="none" 
                                         stroke="currentColor" 
                                         stroke-width="2" 
                                         stroke-linecap="round" 
                                         stroke-linejoin="round">
                                        <path d="m6 9 6 6 6-6"/>
                                    </svg>
                                </button>
                                
                                <div id="projects-accordion-child" 
                                     class="hs-accordion-content w-full overflow-hidden transition-[height] duration-300 hidden">
                                    <ul class="ps-7 pt-1 space-y-1">
                                        @foreach($menu->children() as $child)
                                            <li>
                                                <a class="flex items-center gap-x-3.5 py-2 px-2.5 text-sm text-gray-800 rounded-lg hover:bg-gray-100 focus:outline-none focus:bg-gray-100 dark:text-gray-400 dark:hover:bg-slate-700 dark:focus:bg-slate-700" 
                                                   href="{{ $child->url() }}">
                                                    @if($child->data('icon'))
                                                        <x-icon name="{{ $child->data('icon') }}" class="shrink-0 size-4" />
                                                    @endif
                                                    {{ $child->title }}
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </li>
                        @else
                            <li>
                                <a class="flex items-center gap-x-3.5 py-2 px-2.5 text-sm text-gray-800 rounded-lg hover:bg-gray-100 focus:outline-none focus:bg-gray-100 dark:bg-slate-900 dark:text-white dark:hover:bg-slate-800 dark:focus:bg-slate-800 {{ $menu->isActive() ? 'bg-gray-100 dark:bg-slate-800' : '' }}" 
                                   href="{{ $menu->url() }}">
                                    @if($menu->data('icon'))
                                        <x-icon name="{{ $menu->data('icon') }}" class="shrink-0 size-4" />
                                    @endif
                                    {{ $menu->title }}
                                    @if($menu->data('badge'))
                                        <span class="ms-auto inline-flex items-center py-0.5 px-2 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-slate-700 dark:text-slate-400">
                                            {{ $menu->data('badge') }}
                                        </span>
                                    @endif
                                </a>
                            </li>
                        @endif
                    @endforeach
                    
                </ul>
            </nav>
            
        </div>
        <!-- ========== END CONTENT ========== -->
        
    </div>
</div>
<!-- ========== END SIDEBAR ========== -->