<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RouteController extends Controller
{
    public function index()
    {
        $routes = Route::all();
        return response()->json(['success' => true, 'data' => $routes]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pickup_location' => 'required',
            'destination' => 'required',
            'distance' => 'required|numeric',
            'base_price' => 'required|numeric',
            'status' => 'required|in:active,inactive'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $route = Route::create($request->validated());
        return response()->json(['success' => true, 'data' => $route], 201);
    }

    public function show(Route $route)
    {
        return response()->json(['success' => true, 'data' => $route]);
    }

    public function update(Request $request, Route $route)
    {
        $validator = Validator::make($request->all(), [
            'pickup_location' => 'sometimes|required',
            'destination' => 'sometimes|required',
            'distance' => 'sometimes|numeric',
            'base_price' => 'sometimes|numeric',
            'status' => 'sometimes|in:active,inactive'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $route->update($request->validated());
        return response()->json(['success' => true, 'data' => $route]);
    }

    public function destroy(Route $route)
    {
        $route->delete();
        return response()->json(['success' => true, 'message' => 'Route deleted successfully']);
    }
}
