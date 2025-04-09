<?php

namespace App\Http\Controllers\Api;

use App\Enums\RequestTypes;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RequestTypesController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        return response()->json(RequestTypes::asLabels());
    }
}
