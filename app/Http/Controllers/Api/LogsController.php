<?php

namespace App\Http\Controllers\Api;

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
        return response()->json(EndpointLog::with(['endpoint:id,name'])
            ->orderBy(
                'endpoint_logs.created_at',
                'desc')
            ->paginate(20));
    }
}
