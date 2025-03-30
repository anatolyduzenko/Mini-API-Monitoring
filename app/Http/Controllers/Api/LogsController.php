<?php

namespace App\Http\Controllers\Api;

use App\Enums\StatusCode;
use App\Http\Controllers\Controller;
use App\Models\EndpointLog;
use Illuminate\Http\Request;

class LogsController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $perPage = $request->query('per_page');
        $endpointId = $request->query('endpoint_id');
        $statuses = $request->enums('status_code', StatusCode::class);
        $statusCodes = collect($statuses)->map(fn ($status) => $status->value)->all();

        $query = EndpointLog::with(['endpoint:id,name']);

        if ($endpointId) {
            $query->where('endpoint_id', '=', $endpointId);
        }
        if ($statusCodes) {
            $query->whereIn('status_code', $statusCodes);
        }

        $logsData = $query->orderBy(
            'endpoint_logs.created_at',
            'desc')
            ->paginate($perPage);

        return response()->json($logsData);
    }
}
