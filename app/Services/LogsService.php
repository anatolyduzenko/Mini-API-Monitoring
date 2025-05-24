<?php

namespace App\Services;

use App\Enums\SplitType;
use App\Models\Endpoint;
use App\Models\EndpointLog;
use App\Traits\DateFormatter;
use Illuminate\Support\Facades\DB;

class LogsService
{
    use DateFormatter;

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
            ->where('dashboard_visible', '=', 1)
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
    public function responseTime(int $days, SplitType $splitType)
    {
        $dateSplit = $this->dateFormatter($splitType);

        return DB::table('endpoint_logs')
            ->select(
                DB::raw($dateSplit),
                'endpoints.name',
                'endpoint_id',
                DB::raw('AVG(response_time) as response_time'),
            )
            ->leftJoin('endpoints', 'endpoints.id', '=', 'endpoint_id')
            ->where('endpoint_logs.created_at', '>=', now()->subDays($days))
            ->where('dashboard_visible', '=', 1)
            ->groupBy('date', 'endpoint_id', 'name')
            ->orderBy('date', 'asc')
            ->get();
    }

    public function logSuccess(Endpoint $endpoint, int $status, float $time)
    {
        EndpointLog::create([
            'endpoint_id' => $endpoint->id,
            'status_code' => $status,
            'response_time' => $time,
            'created_at' => now(),
        ]);
    }

    public function logFailure(Endpoint $endpoint, \Exception $e)
    {
        EndpointLog::create([
            'endpoint_id' => $endpoint->id,
            'status_code' => 500,
            'response_time' => null,
            'created_at' => now(),
        ]);
    }
}
