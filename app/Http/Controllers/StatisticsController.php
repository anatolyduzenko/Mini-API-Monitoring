<?php

namespace App\Http\Controllers;

use App\Models\Endpoint;
use Illuminate\Support\Facades\DB;

class StatisticsController extends Controller
{
    /**
     * Get API Uptime Statistics.
     */
    public function getUptime()
    {
        $uptimeStatistics = Endpoint::withCount(['logs as total_checks' => function ($query) {
            $query->select(DB::raw('count(*)'));
        }, 'logs as successful_checks' => function ($query) {
            $query->where('status_code', '>=', 200)
                ->where('status_code', '<', 400)
                ->select(DB::raw('count(*)'));
        }])
            ->get()
            ->map(function ($endpoint) {
                return [
                    'name' => $endpoint->name,
                    'uptime' => $endpoint->total_checks > 0
                        ? round(($endpoint->successful_checks / $endpoint->total_checks) * 100, 2)
                        : 0,
                ];
            });

        return response()->json($uptimeStatistics);
    }

    /**
     * Get Recent API logs
     */
    public function getRecentLogs()
    {
        $recentLogs = DB::table('endpoint_logs')
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
                'endpoint_logs.created_at')
            ->orderBy(
                'endpoint_logs.created_at',
                'desc')
            ->limit(10)
            ->get();

        return response()->json($recentLogs);
    }
}
