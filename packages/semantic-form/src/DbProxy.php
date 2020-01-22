<?php

declare(strict_types=1);

namespace Laravolt\SemanticForm;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class DbProxy
{
    public function __invoke()
    {
        try {
            $payload = decrypt(request('payload'));
            $query = sprintf($payload['query'], trim(request('parent')));
            $results = collect(DB::select($query))->transform(function ($item) use ($payload) {
                $item = (array) $item;

                return [
                    'name' => Arr::get($item, $payload['query_display_column']),
                    'value' => Arr::get($item, $payload['query_key_column']),
                ];
            });
            $json = ['success' => true, 'results' => $results];

            return response()->json($json);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
