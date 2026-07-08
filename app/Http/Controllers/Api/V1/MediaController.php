<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\StoreMediaRequest;
use Illuminate\Http\Request;

class MediaController extends ApiController
{
    public function store(StoreMediaRequest $request)
    {
        $validated = $request->validated();

        return $this->respondSuccess([
            'id' => rand(100, 999),
            'location_id' => $validated['location_id'],
            'url' => 'https://via.placeholder.com/800x600/2a6df4/fff?text=Uploaded+Image',
            'type' => 'image',
            'caption' => $validated['caption'] ?? null,
            'display_order' => 1,
            'created_at' => now()->toISOString(),
            'updated_at' => now()->toISOString(),
        ], 'Media uploaded successfully', 201);
    }

    public function destroy($id)
    {
        return $this->respondSuccess(null, 'Media deleted successfully');
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'orders' => 'required|array',
            'orders.*.id' => 'required|integer',
            'orders.*.display_order' => 'required|integer|min:0',
        ]);

        return $this->respondSuccess(null, 'Media reordered successfully');
    }
}
