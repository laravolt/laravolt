<?php

declare(strict_types=1);

namespace Laravolt\Workflow\Tables;

use Carbon\Carbon;
use Laravolt\Suitable\Builder;
use Laravolt\Suitable\Columns\Raw;
use Laravolt\Suitable\TableView;
use Laravolt\Workflow\Entities\Module;
use Laravolt\Workflow\Entities\ViewQuery;

abstract class Table extends TableView implements \Laravolt\Workflow\Contracts\Table
{
    /** @var Module */
    protected $module;

    public function init()
    {
        $this->decorate(function (Builder $builder) {
        });
    }

    public function setModule(Module $moduleId)
    {
        $this->module = $moduleId;

        return $this;
    }

    public function buttons()
    {
        return \Laravolt\Workflow\Tables\Columns\ProcessInstanceButton::make()
            ->setModule($this->module);
    }

    protected function duration($column = 'created_at')
    {
        return Raw::make(function ($item) use ($column) {
            $value = $item->{$column};

            try {
                if ($value == null) {
                    $value = Carbon::now();
                }

                $duration = Carbon::make($value)->diffInDays();
                switch ($duration) {
                    case 0:
                        return sprintf('<div class="ui basic label mini blue">%s</div>', 'Baru');

                        break;
                    case $duration <= 2:
                        return sprintf('<div class="ui basic label mini green">%s hari</div>', $duration);

                        break;
                    case $duration <= 5:
                        return sprintf('<div class="ui basic label mini orange">%s hari</div>', $duration);

                        break;
                    case $duration <= 10:
                        return sprintf('<div class="ui basic label mini red">%s hari</div>', $duration);

                        break;
                    default:
                        return sprintf('<div class="ui label mini red">%s hari</div>', $duration);

                        break;
                }
            } catch (\Exception $e) {
                return $value;
            }
        }, 'Durasi')->setHeaderAttributes(['style' => 'width:80px']);
    }

    public function viewQuery(): ?ViewQuery
    {
        return null;
    }
}
