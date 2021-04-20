<x-laravolt::layout.app :title="__('Edit Password')">
    <x-laravolt::panel title="{{ __('Edit Password') }}" icon="user-lock">
        {!! form()->open()->action(route('my::password.update'))->horizontal() !!}
        {!! form()->password('password_current')->label(__('laravolt::users.password_current')) !!}
        {!! form()->password('password')->label(__('laravolt::users.password_new')) !!}
        {!! form()->password('password_confirmation')->label(__('laravolt::users.password_new_confirmation')) !!}
        {!! form()->action(form()->submit(__('laravolt::action.save'))) !!}
        {!! form()->close() !!}
    </x-laravolt::panel>
</x-laravolt::layout.app>
