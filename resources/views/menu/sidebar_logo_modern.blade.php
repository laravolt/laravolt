<!-- Logo -->
<div class="flex justify-center items-center">
    <a class="flex-none rounded-xl text-xl inline-block font-semibold focus:outline-none focus:opacity-80" 
       href="{{ route('home') ?? '/' }}" 
       aria-label="{{ config('app.name') }}">
        
        @if(config('laravolt.ui.logo'))
            <img src="{{ config('laravolt.ui.logo') }}" 
                 alt="{{ config('app.name') }}" 
                 class="w-auto h-8">
        @else
            <span class="text-gray-800 dark:text-white">
                {{ config('app.name') }}
            </span>
        @endif
        
    </a>
</div>