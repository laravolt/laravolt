<?php

namespace Laravolt\Epicentrum\Table;

use Laravolt\Epicentrum\Repositories\RepositoryInterface;
use Laravolt\Suitable\Columns\Avatar;
use Laravolt\Suitable\Columns\Date;
use Laravolt\Suitable\Columns\Label;
use Laravolt\Suitable\Columns\Numbering;
use Laravolt\Suitable\Columns\Raw;
use Laravolt\Suitable\Columns\RestfulButton;
use Laravolt\Suitable\Columns\Text;
use Laravolt\Suitable\TableView;

class UserTable extends TableView
{
    protected $showPerPage = true;

    public function source()
    {
        return app(RepositoryInterface::class)->paginate(request());
    }

    protected function columns()
    {
        return [
            Numbering::make('No'),
            Avatar::make('name', ''),
            Text::make('name', trans('laravolt::users.name'))->sortable(),
            Text::make('email', trans('laravolt::users.email'))
                ->sortable()
                ->searchable('email', \Laravolt\Suitable\Headers\Search\SelectHeader::make('email', [1, 2, 3])),
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
}
