<?php

namespace App\Http\Controllers;

use App\Models\Endpoint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatisticsController extends Controller
{
    /**
     * Get API Uptime Statistics.
     */
    public function getUptime(Request $request)
    {
        $perPage = $request->query('per_page', 8);

        $uptimeStatistics = Endpoint::withCount(['logs as total_checks' => function ($query) {
            $query->select(DB::raw('count(*)'));
        }, 'logs as successful_checks' => function ($query) {
            $query->where('status_code', '>=', 200)
                ->where('status_code', '<', 400)
                ->select(DB::raw('count(*)'));
        }])
            ->paginate($perPage)
            ->through(function ($endpoint) {
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
            ->limit(9)
            ->get();

        return response()->json($recentLogs);
    }

    /**
     * Get API Uptime Statistics Over Time.
     * Use one week as period
     */
    public function getUptimeGraph(Request $request)
    {
        $days = $request->query('days', 7);

        $trendData = DB::table('endpoint_logs')
            ->select(
                DB::raw('DATE(endpoint_logs.created_at) as date'),
                'endpoints.name',
                'endpoint_id',
                DB::raw('SUM(CASE WHEN status_code >= 200 AND status_code < 400 THEN 1 ELSE 0 END) as successful_checks'),
                DB::raw('COUNT(*) as total_checks')
            )
            ->leftJoin('endpoints', 'endpoints.id', '=', 'endpoint_id')
            ->where('endpoint_logs.created_at', '>=', now()->subDays($days))
            ->groupBy('date', 'endpoint_id', 'name')
            ->orderBy('date', 'asc')
            ->get();

        $graphData = $trendData->groupBy('date')
            ->map(function ($entries, $date) {
                $row = ['date' => $date];
                foreach ($entries as $entry) {
                    $uptime = $entry->total_checks > 0
                        ? round(($entry->successful_checks / $entry->total_checks) * 100, 2)
                        : 0;
                    $row[$entry->name] = $uptime;
                }

                return $row;
            })->values();

        $endpointLabels = $trendData->map(function ($entry) {
            return $entry->name;
        })->unique()->values();

        return response()->json(['labels' => $endpointLabels, 'graphData' => $graphData]);
    }

    /**
     * Get Average API Response Time.
     */
    public function getResponseTime(Request $request)
    {
        $days = $request->query('days', 7);

        $responseTime = DB::table('endpoint_logs')
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

        $graphData = $responseTime->groupBy('date')
            ->map(function ($entries, $date) {
                $row = ['date' => $date];
                foreach ($entries as $entry) {
                    $row[$entry->name] = round($entry->response_time, 2);
                }

                return $row;
            })->values();

        $endpointLabels = $responseTime->map(function ($entry) {
            return $entry->name;
        })->unique()->values();

        return response()->json(['labels' => $endpointLabels, 'graphData' => $graphData]);
    }
}
