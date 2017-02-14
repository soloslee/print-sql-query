<?php

namespace Soloslee\PrintSqlQuery;

use DB;
use Cache;
use Config;
use Closure;
use Carbon\Carbon;

class PrintSqlQuery
{
    const MINUTES_CACHE = 10;

    const CACHE_KEY = 'print_sql_query';

    public function handle($request, Closure $next)
    {
        $log = Cache::get(self::CACHE_KEY);

        if ($log === NULL) {
            $log = env(self::CACHE_KEY);
            Cache::put(self::CACHE_KEY, $log, self::MINUTES_CACHE);
        }

        if ($log) {
            DB::enableQueryLog();
        }

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
        if (! Cache::get(self::CACHE_KEY)) {
            return;
        }

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
