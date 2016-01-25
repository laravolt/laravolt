<?php

namespace Laravolt\SemanticForm\Middleware;

use Carbon\Carbon;
use Closure;

class SelectDateMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        $fields = array_slice(func_get_args(), 2);

        foreach ($fields as $field) {
            if ($request->has($field)) {
                $request->merge([$field => $this->formatDate($request->get($field))]);
            } elseif ($date = $request->old($field)) {
                $request->merge($this->splitDate($date));
            }
        }

        return $next($request);
    }

    protected function formatDate($dates)
    {
        return sprintf('%s-%s-%s', $dates['year'], $dates['month'], $dates['date']);
    }

    protected function splitDate($date)
    {
        $date = Carbon::createFromFormat('Y-m-d', $date);

        return [
            $date => [
                'date'  => $date->day,
                'month' => $date->month,
                'year'  => $date->year,
            ]
        ];
    }
}
