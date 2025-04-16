<?php

namespace App\Services;

use App\Enums\SplitType;
use App\Enums\StatusCode;
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
        return Endpoint::withCount(['logs as total_checks' => function ($query) {
            $query->select(DB::raw('count(*)'));
        }, 'logs as successful_checks' => function ($query) {
            $query->where('status_code', '>=', StatusCode::OK->value)
                ->where('status_code', '<', StatusCode::BAD_REQUEST->value)
                ->select(
                    DB::raw('count(*)')
                );
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
    }

    /**
     * Prepares data for uptime Chart
     *
     * @param  int|null  $endpointId
     * @return \Illuminate\Support\Collection<int, \stdClass>
     */
    public function uptimeTrendData(int $days, SplitType $splitType = SplitType::DAILY, $endpointId = null)
    {
        $dateSplit = $this->dateFormatter($splitType);

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

        return $query->groupBy('date', 'endpoint_id', 'name')
            ->orderBy('date', 'asc')
            ->get();
    }

    private function dateFormatter(SplitType $splitType)
    {
        return match ($splitType) {
            SplitType::DAILY => 'DATE(endpoint_logs.created_at) as date',
            SplitType::HOURLY => 'DATE_FORMAT(endpoint_logs.created_at, \'%Y-%m-%d %H:00:00\') as date',
            SplitType::DECAMIN => 'DATE_FORMAT(endpoint_logs.created_at, \'%Y-%m-%d %H:%i:00\') as date',
        };
    }
}
