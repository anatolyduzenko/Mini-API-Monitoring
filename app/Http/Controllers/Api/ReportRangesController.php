<?php

namespace App\Http\Controllers\Api;

use App\Enums\ReportRange;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReportRangesController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        return response()->json(ReportRange::asLabels());
    }
}
