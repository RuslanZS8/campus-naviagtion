<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CampusController extends Controller
{
    public function index()
    {
        $nodes = config('campus.nodes');
        return view('campus', compact('nodes'));
    }

    public function nodes()
    {
        return response()->json(config('campus.nodes'));
    }

    public function route(Request $request)
    {
        $start = $request->query('start');
        $end   = $request->query('end');
        $url = "https://router.project-osrm.org/route/v1/walking/{$start};{$end}?overview=full&geometries=geojson";
        $response = file_get_contents($url);
        return response($response, 200)->header('Content-Type', 'application/json');
    }
}