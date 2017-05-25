<?php

namespace Soloslee\PrintSqlQuery;

use DB;
use Cache;
use Closure;

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

    private function toStr($value)
    {
        if (is_string($value)) {
            return "'{$value}'";
        }

        return (string)$value;
    }

    public function terminate($request, $response)
    {
        if (! Cache::get($this->cacheKey)) {
            return;
        }

        error_log('Queries for route: ' . $request->path());

        foreach (DB::getQueryLog() as $q) {
            foreach ($q['bindings'] as $binding) {
                $sql = preg_replace('/\?/', $this->toStr($binding), $q['query'], 1);
            }

            error_log($sql . ' [' . $q['time'] . 'ms]');
        }
    }
}
