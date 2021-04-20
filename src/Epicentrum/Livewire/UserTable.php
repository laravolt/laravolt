<?php

namespace Laravolt\Epicentrum\Livewire;

use Laravolt\Epicentrum\Filters\EmailFilter;
use Laravolt\Epicentrum\Filters\RegisteredFilter;
use Laravolt\Epicentrum\Filters\RoleFilter;
use Laravolt\Epicentrum\Filters\StatusFilter;
use Laravolt\Suitable\Columns\Avatar;
use Laravolt\Suitable\Columns\Date;
use Laravolt\Suitable\Columns\Label;
use Laravolt\Suitable\Columns\Numbering;
use Laravolt\Suitable\Columns\Raw;
use Laravolt\Suitable\Columns\RestfulButton;
use Laravolt\Suitable\Columns\Text;
use Laravolt\Ui\TableView;

class UserTable extends TableView
{
    public function data()
    {
        $sortPayload = [
            'sort' => $this->sort,
            'direction' => $this->direction,
        ];

        $query = app(config('auth.providers.users.model'))
            ->with('roles')
            ->autoSort('sort', 'direction', $sortPayload)
            ->autoFilter()
            ->latest();

        $keyword = trim($this->search);
        if ($keyword !== '') {
            $searchabledColumns = config('laravolt.epicentrum.repository.searchable', []);
            $query->whereLike($searchabledColumns, $keyword);
        }

        return $query;
    }

    public function columns(): array
    {
        return [
            Numbering::make('No'),
            Avatar::make('name', ''),
            Text::make('name', trans('laravolt::users.name'))->sortable(),
            Text::make('email', trans('laravolt::users.email'))->sortable(),
            Raw::make(
                function ($data) {
                    return $data->roles->implode('name', ', ');
                },
                trans('laravolt::users.roles')
            ),
            Label::make('status', trans('laravolt::users.status'))->addClass('mini'),
            Date::make('created_at', trans('laravolt::users.registered_at'))->sortable(),
            RestfulButton::make('epicentrum::users', trans('laravolt::users.action'))->only('edit', 'delete'),
        ];
    }

    public function filters(): array
    {
        return [
            new EmailFilter(),
            new RoleFilter(),
            new StatusFilter(),
            new RegisteredFilter(),
        ];
    }
}
