<?php

declare(strict_types=1);

namespace Laravolt\Camunda\Controllers\Actions;

use Illuminate\Support\Str;
use Laravolt\Camunda\Entities\Module;
use Laravolt\Camunda\Models\AutoSave as AutoSaveModel;

class AutoSave
{
    public function __invoke(Module $module, $taskId)
    {
        $primaryData = [
            'task_id' => $taskId,
            'user_id' => auth()->id(),
        ];
        $data = collect(request()->all())
            ->reject(function ($value, $key) {
                return Str::startsWith($key, '_');
            });
        $autosave = AutoSaveModel::updateOrCreate($primaryData, ['data' => $data]);

        return response()->json($autosave->toArray());
    }
}
