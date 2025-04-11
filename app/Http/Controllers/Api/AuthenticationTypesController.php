<?php

namespace App\Http\Controllers\Api;

use App\Enums\AuthenticationType;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthenticationTypesController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        return response()->json(AuthenticationType::asLabels());
    }
}
