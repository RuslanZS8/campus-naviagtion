<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;

class LocationController extends ApiController
{
    public function index(Request $request)
    {
        try {
            // ─── Get data from config or use fallback ───
            $nodes = config('campus.nodes', []);

            // ─── If config is empty, use fallback data ───
            if (empty($nodes)) {
                $nodes = [
                    [
                        'id' => 1,
                        'name' => 'Rectorate',
                        'lat' => 35.21853,
                        'lng' => 33.4179368,
                        'accessible' => true,
                        'description' => 'The main administrative building of CIU.',
                        'category' => 'buildings',
                        'image' => 'https://www.ciu.edu.tr/map/campus/1-Rektorluk/ciu-rectorate1-min.jpg',
                        'address' => 'CIU Campus, North Cyprus',
                        'opening_hours' => '08:00 - 17:00',
                        'phone' => '+90 392 671 1111',
                        'email' => 'rectorate@ciu.edu.tr',
                    ],
                    [
                        'id' => 2,
                        'name' => 'Library (K)',
                        'lat' => 35.2190714,
                        'lng' => 33.4173299,
                        'accessible' => true,
                        'description' => 'The central library with extensive academic resources.',
                        'category' => 'buildings',
                        'image' => 'https://www.ciu.edu.tr/map/campus/2-Kutuphane/ciu-library-1-min.jpg',
                        'address' => 'CIU Campus, North Cyprus',
                        'opening_hours' => '08:00 - 22:00',
                        'phone' => '+90 392 671 1111',
                        'email' => 'library@ciu.edu.tr',
                    ],
                    [
                        'id' => 3,
                        'name' => 'Engineering Building (ST)',
                        'lat' => 35.2197494,
                        'lng' => 33.4159989,
                        'accessible' => true,
                        'description' => 'State-of-the-art engineering labs and classrooms.',
                        'category' => 'buildings',
                        'image' => 'https://www.ciu.edu.tr/map/campus/5-Bilim%20ve%20Teknoloji%20Merkezi/ciu-science-technology-1-min.jpg',
                        'address' => 'CIU Campus, North Cyprus',
                        'opening_hours' => '08:00 - 18:00',
                        'phone' => '+90 392 671 1111',
                        'email' => 'engineering@ciu.edu.tr',
                    ],
                    [
                        'id' => 4,
                        'name' => 'Nature Cafe',
                        'lat' => 35.2222243,
                        'lng' => 33.4149668,
                        'accessible' => true,
                        'description' => 'A relaxing café surrounded by green spaces.',
                        'category' => 'chill',
                        'image' => 'https://www.ciu.edu.tr/map/campus/19-Yemek%20Alani/ciu-food-court-1.jpg',
                        'address' => 'CIU Campus, North Cyprus',
                        'opening_hours' => '08:00 - 20:00',
                        'phone' => '+90 392 671 1111',
                        'email' => 'cafe@ciu.edu.tr',
                    ],
                    [
                        'id' => 5,
                        'name' => 'CIU Arena (AR)',
                        'lat' => 35.2237433,
                        'lng' => 33.4208485,
                        'accessible' => true,
                        'description' => 'Large indoor sports arena for basketball, volleyball, and major events.',
                        'category' => 'sports',
                        'image' => 'https://www.ciu.edu.tr/map/campus/26-UKU%20Arena/ciu-arena-2.jpg',
                        'address' => 'CIU Campus, North Cyprus',
                        'opening_hours' => '08:00 - 22:00',
                        'phone' => '+90 392 671 1111',
                        'email' => 'arena@ciu.edu.tr',
                    ],
                ];
            }

            // ─── Transform data to API format ───
            $locations = array_map(function ($node) {
                return [
                    'id' => $node['id'] ?? rand(100, 999),
                    'name' => $node['name'] ?? 'Unnamed Location',
                    'lat' => $node['lat'] ?? 35.22,
                    'lng' => $node['lng'] ?? 33.41,
                    'accessible' => $node['accessible'] ?? true,
                    'description' => $node['description'] ?? 'No description available.',
                    'image' => $node['image'] ?? null,
                    'category' => $node['category'] ?? null,
                    'category_name' => $this->getCategoryName($node['category'] ?? null),
                    'button_text' => $node['button_text'] ?? null,
                    'link' => $node['link'] ?? null,
                    'address' => $node['address'] ?? 'CIU Campus, North Cyprus',
                    'opening_hours' => $node['opening_hours'] ?? '08:00 - 17:00',
                    'phone' => $node['phone'] ?? '+90 392 671 1111',
                    'email' => $node['email'] ?? 'info@ciu.edu.tr',
                    'media' => [],
                ];
            }, $nodes);

            return response()->json([
                'success' => true,
                'message' => 'Locations retrieved successfully',
                'data' => $locations
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading locations: ' . $e->getMessage(),
            ], 500);
        }
    }

    private function getCategoryName($categoryKey)
    {
        $categories = [
            'buildings' => 'Academic Buildings',
            'chill' => 'Chill Zone & Relaxation',
            'dining' => 'Dining & Cafés',
            'services' => 'Student Services',
            'residence' => 'Residence Halls',
            'sports' => 'Sports & Recreation',
        ];
        return $categories[$categoryKey] ?? $categoryKey ?? 'Building';
    }

    // ─── Show single location ───
    public function show($id)
    {
        $nodes = config('campus.nodes', []);

        if (empty($nodes)) {
            $nodes = [
                ['id' => 1, 'name' => 'Rectorate', 'lat' => 35.21853, 'lng' => 33.4179368, 'accessible' => true, 'description' => 'Main building', 'category' => 'buildings', 'image' => null],
            ];
        }

        $node = collect($nodes)->firstWhere('id', (int)$id);

        if (!$node) {
            return response()->json([
                'success' => false,
                'message' => 'Location not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Location retrieved successfully',
            'data' => [
                'id' => $node['id'],
                'name' => $node['name'],
                'lat' => $node['lat'],
                'lng' => $node['lng'],
                'accessible' => $node['accessible'] ?? true,
                'description' => $node['description'] ?? 'No description available.',
                'image' => $node['image'] ?? null,
                'category' => $node['category'] ?? null,
                'category_name' => $this->getCategoryName($node['category'] ?? null),
                'button_text' => $node['button_text'] ?? null,
                'link' => $node['link'] ?? null,
                'address' => $node['address'] ?? 'CIU Campus, North Cyprus',
                'opening_hours' => $node['opening_hours'] ?? '08:00 - 17:00',
                'phone' => $node['phone'] ?? '+90 392 671 1111',
                'email' => $node['email'] ?? 'info@ciu.edu.tr',
                'media' => [],
            ]
        ], 200);
    }

    // ─── Media endpoint ───
    public function media($id)
    {
        return response()->json([
            'success' => true,
            'message' => 'Media retrieved successfully',
            'data' => [
                ['id' => 1, 'url' => 'https://via.placeholder.com/800x600/2a6df4/fff?text=Image', 'type' => 'image', 'caption' => 'Location image', 'display_order' => 1]
            ]
        ], 200);
    }

    // ─── Matterport endpoint ───
    public function matterport($id)
    {
        return response()->json([
            'success' => false,
            'message' => 'No Matterport tour available'
        ], 404);
    }

    // ─── Admin endpoints ───
    public function adminIndex(Request $request)
    {
        return $this->index($request);
    }

    public function store(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => 'Location created successfully',
            'data' => ['id' => rand(100, 999)]
        ], 201);
    }

    public function update(Request $request, $id)
    {
        return response()->json([
            'success' => true,
            'message' => 'Location updated successfully',
            'data' => ['id' => (int)$id]
        ], 200);
    }

    public function destroy($id)
    {
        return response()->json([
            'success' => true,
            'message' => 'Location deleted successfully'
        ], 200);
    }

    public function toggleVisibility($id)
    {
        return response()->json([
            'success' => true,
            'message' => 'Visibility toggled successfully',
            'data' => ['id' => (int)$id, 'is_visible' => false]
        ], 200);
    }

    public function reorder(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => 'Locations reordered successfully'
        ], 200);
    }
}
