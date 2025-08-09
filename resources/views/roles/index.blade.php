<x-volt-app :title="__('laravolt::label.roles')">

    <x-slot name="actions">
        <x-volt-link-button
                :url="route('epicentrum::roles.create')"
                icon="plus"
                :label="__('laravolt::action.add')"/>
    </x-slot>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @foreach($roles as $role)
            <a href="{{ route('epicentrum::roles.edit', $role['id']) }}" class="block rounded-xl border border-gray-200 bg-white shadow-sm hover:shadow-md transition-shadow">
                <div class="px-4 py-3">
                    <h3 class="text-base font-semibold text-gray-800">{{ $role['name'] }}</h3>
                </div>
                <div class="flex items-center justify-between border-t border-gray-200 px-4 py-2 text-sm text-gray-600">
                    <span class="inline-flex items-center gap-x-1"><svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-4-4h-1M7 20H2v-2a4 4 0 014-4h1m0-4a4 4 0 118 0v4a4 4 0 11-8 0V8z"/></svg>{{ $role->users_count }}</span>
                    <span class="inline-flex items-center gap-x-1"><svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/></svg>{{ $role->permissions_count }}</span>
                </div>
            </a>
        @endforeach
    </div>

</x-volt-app>
