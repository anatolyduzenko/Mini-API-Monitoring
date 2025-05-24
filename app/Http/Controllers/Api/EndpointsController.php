<?php

namespace App\Http\Controllers\Api;

use AnatolyDuzenko\ConfigurablePrometheus\Contracts\MetricManagerInterface;
use App\Enums\StatusCode;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreEndpointRequest;
use App\Http\Requests\Api\UpdateEndpointRequest;
use App\Models\Endpoint;
use Illuminate\Http\Request;

class EndpointsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->boolean('all')) {
            return response()->json(
                Endpoint::orderBy('name')->get(),
                StatusCode::OK->value
            );
        }

        return response()->json(Endpoint::paginate(10), StatusCode::OK->value);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEndpointRequest $request, MetricManagerInterface $metrics)
    {
        $newEndpoint = Endpoint::create($request->validated());

        if (! $newEndpoint) {
            return response()->json(['error' => 'Failed to create endpoint'], StatusCode::INTERNAL_SERVER_ERROR->value);
        }

        $metrics->set('endpoints', 'endpoints_total', Endpoint::count(), ['total']);

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
    public function destroy(Endpoint $endpoint, MetricManagerInterface $metrics)
    {
        $endpoint->delete();

        $metrics->set('endpoints', 'endpoints_total', Endpoint::count(), ['total']);

        return response()->json(['message' => 'Deleted successfully'], StatusCode::NO_CONTENT->value);
    }

    /**
     * Toggles endpoint visibility
     */
    public function toggleVisibility(Endpoint $endpoint)
    {
        $endpoint->dashboard_visible = ! $endpoint->dashboard_visible;
        $endpoint->save();

        return response()->json(['success' => true, 'show_on_dashboard' => $endpoint->dashboard_visible]);
    }
}
