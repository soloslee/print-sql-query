<?php

namespace Soloslee\PrintSqlQuery;

use DB;
use Cache;
use Config;
use Closure;
use Carbon\Carbon;

class PrintSqlQuery
{
    private $cacheMinutes = 10;

    private $cacheKey = 'PRINT_SQL_QUERY';

    public function handle($request, Closure $next)
    {
        $log = Cache::get($this->cacheKey);

        if ($log === NULL) {
            $log = env($this->cacheKey);
            Cache::put($this->cacheKey, $log, $this->cacheMinutes);
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
        if (! Cache::get($this->cacheKey)) {
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
