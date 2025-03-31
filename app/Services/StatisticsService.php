<?php

namespace App\Services;

use App\Enums\SplitTypes;
use App\Models\Endpoint;
use Illuminate\Support\Facades\DB;

class StatisticsService
{
    /**
     * Prepares data for Uptime Report
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function reportUptime(int $perPage)
    {
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
                    'id' => $endpoint->id,
                    'name' => $endpoint->name,
                    'uptime' => $endpoint->total_checks > 0
                        ? round(($endpoint->successful_checks / $endpoint->total_checks) * 100, 2)
                        : 0,
                ];
            });

        return $uptimeStatistics;
    }

    /**
     * Prepares data for uptime Chart
     *
     * @return \Illuminate\Support\Collection<int, \stdClass>
     */
    public function uptimeTrendData(int $days, SplitTypes $splitType = SplitTypes::DAILY, $endpointId = null)
    {
        $dateSplit = match ($splitType) {
            SplitTypes::DAILY => 'DATE(endpoint_logs.created_at) as date',
            SplitTypes::HOURLY => 'DATE_FORMAT(endpoint_logs.created_at, \'%Y-%m-%d %H:00:00\') as date',
            SplitTypes::DECAMIN => 'DATE_FORMAT(endpoint_logs.created_at, \'%Y-%m-%d %H:%i:00\') as date',
        };

        $query = DB::table('endpoint_logs')
            ->select(
                DB::raw($dateSplit),
                'endpoints.name',
                'endpoint_id',
                DB::raw('SUM(CASE WHEN status_code >= 200 AND status_code < 400 THEN 1 ELSE 0 END) as successful_checks'),
                DB::raw('COUNT(*) as total_checks')
            )
            ->leftJoin('endpoints', 'endpoints.id', '=', 'endpoint_id')
            ->where('endpoint_logs.created_at', '>=', now()->subDays($days));

        if ($endpointId) {
            $query->where('endpoint_id', '=', $endpointId);
        }

        $trendData = $query->groupBy('date', 'endpoint_id', 'name')
            ->orderBy('date', 'asc')
            ->get();

        return $trendData;
    }
}
