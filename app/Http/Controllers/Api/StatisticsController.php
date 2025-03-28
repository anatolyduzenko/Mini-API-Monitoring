<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\LogsService;
use App\Services\StatisticsService;
use Illuminate\Http\Request;

class StatisticsController extends Controller
{
    protected $statisticsService;

    protected $logsService;

    public function __construct(StatisticsService $statisticsService, LogsService $logsService)
    {
        $this->statisticsService = $statisticsService;
        $this->logsService = $logsService;
    }

    /**
     * Get API Uptime Statistics.
     */
    public function getUptime(Request $request)
    {
        $perPage = $request->query('per_page', 8);

        $uptimeStatistics = $this->statisticsService->reportUptime($perPage);

        return response()->json($uptimeStatistics);
    }

    /**
     * Get Recent API logs
     */
    public function getRecentLogs()
    {
        $recentLogs = $this->logsService->recentLogs();

        return response()->json($recentLogs);
    }

    /**
     * Get API Uptime Statistics Over Time. Uses one week as period.
     *
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function getUptimeGraph(Request $request)
    {
        $days = $request->query('days', 7);

        $trendData = $this->statisticsService->uptimeTrendData($days);

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
     *
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function getResponseTime(Request $request)
    {
        $days = $request->query('days', 7);

        $responseTime = $this->logsService->responseTime($days);

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
