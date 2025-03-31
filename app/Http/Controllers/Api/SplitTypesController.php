<?php

namespace App\Http\Controllers\Api;

use App\Enums\SplitTypes;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SplitTypesController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        return response()->json(SplitTypes::asLabels());
    }
}
