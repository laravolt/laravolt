<x-volt-app :title="__('laravolt::label.permissions')">

    <x-volt-panel title="Atur Deskripsi Hak Akses">
        <div class="flex items-start gap-x-3 rounded-md border border-yellow-200 bg-yellow-50 p-3 text-sm text-yellow-800">
            <svg class="h-5 w-5 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M12 9v2m0 6h.01M12 5a7 7 0 100 14 7 7 0 000-14z"/></svg>
            <div>
                <div class="font-semibold">Tips</div>
                <p>
                    Memberikan deskripsi yang jelas akan membantu admin aplikasi ketika melakukan pengaturan hak akses.
                    <br>
                    Contoh: <strong>laravolt::manage-user</strong> bisa diperjelas deskripsinya menjadi <strong>Menambah, mengedit, dan menghapus pengguna</strong>.
                </p>
            </div>
        </div>
        {!! form()->open(route('epicentrum::permissions.update'))->put() !!}

        {!! Suitable::source($permissions)->columns([
            \Laravolt\Suitable\Columns\Numbering::make('No')->setHeaderAttributes(['width' => '50px']),
            \Laravolt\Suitable\Columns\Text::make('name', __('laravolt::permissions.name'))
                ->setHeaderAttributes(['width' => '250px']),
            \Laravolt\Suitable\Columns\Raw::make(function($item) {
                return Form::textarea('permission['.$item->getKey().']')->value($item->description)->rows(2);
            }, __('laravolt::permissions.description'))
        ])->render() !!}

        {!! form()->submit(__('laravolt::action.save')) !!}

        {!! form()->close() !!}

    </x-volt-panel>

</x-volt-app>
