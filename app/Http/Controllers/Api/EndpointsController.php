<?php

namespace App\Http\Controllers\Api;

use App\Enums\StatusCode;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreEndpointRequest;
use App\Http\Requests\Api\UpdateEndpointRequest;
use App\Models\Endpoint;

class EndpointsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Endpoint::paginate(10), StatusCode::OK->value);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEndpointRequest $request)
    {
        $newEndpoint = Endpoint::create($request->validated());

        if (! $newEndpoint) {
            return response()->json(['error' => 'Failed to create endpoint'], StatusCode::INTERNAL_SERVER_ERROR->value);
        }

        return response()->json($newEndpoint, StatusCode::CREATED->value);
    }

    /**
     * Display the specified resource.
     */
    public function show(Endpoint $endpoint)
    {
        return response()->json($endpoint);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEndpointRequest $request, Endpoint $endpoint)
    {
        $updatedEndpoint = $endpoint->update($request->validated());

        if (! $updatedEndpoint) {
            return response()->json(['error' => 'Failed to update endpoint'], StatusCode::INTERNAL_SERVER_ERROR->value);
        }

        return response()->json($updatedEndpoint, StatusCode::OK->value);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Endpoint $endpoint)
    {
        $endpoint->delete();

        return response()->json(['message' => 'Deleted successfully'], StatusCode::NO_CONTENT->value);
    }
}
