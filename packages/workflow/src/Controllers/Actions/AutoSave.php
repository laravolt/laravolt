<?php

declare(strict_types=1);

namespace Laravolt\Workflow\Controllers\Actions;

use Illuminate\Support\Str;
use Laravolt\Workflow\Entities\Module;

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
        $autosave = \Laravolt\Workflow\Models\AutoSave::updateOrCreate($primaryData, ['data' => $data]);

        return response()->json($autosave->toArray());
    }
}
