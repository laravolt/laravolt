<?php

namespace Laravolt\SemanticForm\Middleware;

use Closure;
use Illuminate\Http\Request;

class SelectDateTimeMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $fields = array_slice(func_get_args(), 2);

        foreach ($fields as $field) {
            $hiddenField = '_'.$field;
            if ($request->has($hiddenField)) {
                $request->merge([$field => $this->formatDate($request->get($hiddenField))]);
            }
        }

        return $next($request);
    }

    protected function formatDate($dates)
    {
        return sprintf('%s-%s-%s %s:00', $dates['year'], $dates['month'], $dates['date'], $dates['time']);
    }
}
