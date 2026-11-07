<?php

declare(strict_types=1);

namespace Laravolt\SemanticForm;

use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class DbProxy
{
    public function __invoke()
    {
        try {
            $results = [];
            $term = request('term');

            if (is_string($term) && $term) {
                $payload = decrypt(request('payload'));
                [$query, $bindings] = DbProxyQuery::bind($payload['query'], mb_trim($term));
                $connection = $payload['connection'] ?? config('database.default');
                $results = collect(DB::connection($connection)->select($query, $bindings))->transform(function ($item) use ($payload) {
                    $item = (array) $item;

                    return [
                        'name' => Arr::get($item, $payload['query_display_column']),
                        'value' => Arr::get($item, $payload['query_key_column']),
                    ];
                });
            }

            $json = ['success' => true, 'results' => $results];

            return response()->json($json);
        } catch (Exception $e) {
            report($e);

            return response()->json(['success' => false, 'message' => __('Unable to load the options.')]);
        }
    }
}
