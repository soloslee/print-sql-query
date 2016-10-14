<?php

namespace Soloslee\PrintSqlQuery;

use DB;
use Closure;
use Carbon\Carbon;

class PrintSqlQuery
{
    public function handle($request, Closure $next)
    {
        DB::enableQueryLog();

        return $next($request);
    }

    private function queryValueToString($vlaue)
    {
        if ($vlaue instanceof Carbon) {
            $vlaue = (string) $vlaue;
        }

        if (is_string($vlaue)) {
            return "'{$vlaue}'";
        } elseif (is_bool($vlaue)) {
            return (string) (int) $vlaue;
        } else {
            return (string) $vlaue;
        }
    }

    public function terminate($request, $response)
    {
        $queries = DB::getQueryLog();
        error_log('Queries for route: ' . $request->path());

        foreach ($queries as $query) {
            $sql = $query['query'];

            foreach ($query['bindings'] as $binding) {
                $sql = preg_replace('/\?/', $this->queryValueToString($binding), $sql, 1);
            }

            error_log($sql . ' [' . $query['time'] . 'ms]');
        }
    }
}
