<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class LogsService
{
    /**
     * Returns Recent API logs
     *
     * @return \Illuminate\Support\Collection<int, \stdClass>
     */
    public function recentLogs()
    {
        return DB::table('endpoint_logs')
            ->join(
                'endpoints',
                'endpoint_logs.endpoint_id',
                '=',
                'endpoints.id'
            )
            ->select(
                'endpoints.name',
                'endpoint_logs.status_code',
                'endpoint_logs.response_time',
                'endpoint_logs.created_at'
            )
            ->orderBy(
                'endpoint_logs.created_at',
                'desc'
            )
            ->limit(9)
            ->get();
    }

    /**
     * Prepares data for response time chart
     *
     * @return \Illuminate\Support\Collection<int, \stdClass>
     */
    public function responseTime(int $days)
    {
        return DB::table('endpoint_logs')
            ->select(
                DB::raw('DATE(endpoint_logs.created_at) as date'),
                'endpoints.name',
                'endpoint_id',
                DB::raw('AVG(response_time) as response_time'),
            )
            ->leftJoin('endpoints', 'endpoints.id', '=', 'endpoint_id')
            ->where('endpoint_logs.created_at', '>=', now()->subDays($days))
            ->groupBy('date', 'endpoint_id', 'name')
            ->orderBy('date', 'asc')
            ->get();
    }
}
