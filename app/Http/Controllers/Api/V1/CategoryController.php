<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends ApiController
{
    // ─── PUBLIC ENDPOINTS ───

    public function index()
    {
        return $this->respondSuccess([
            ['id' => 1, 'name' => 'Academic Buildings', 'slug' => 'academic-buildings', 'description' => 'University academic buildings and faculties', 'is_visible' => true, 'children' => []],
            ['id' => 2, 'name' => 'Student Services', 'slug' => 'student-services', 'description' => 'Student support and administrative services', 'is_visible' => true, 'children' => []],
            ['id' => 3, 'name' => 'Dining & Cafés', 'slug' => 'dining-cafes', 'description' => 'Restaurants and cafés on campus', 'is_visible' => true, 'children' => []],
        ], 'Categories retrieved successfully');
    }

    public function show($slug)
    {
        return $this->respondSuccess([
            'id' => 1,
            'name' => 'Academic Buildings',
            'slug' => $slug,
            'description' => 'University academic buildings and faculties',
            'is_visible' => true,
            'locations' => [],
        ], 'Category retrieved successfully');
    }

    // ─── ADMIN ENDPOINTS ───

    public function adminIndex()
    {
        return $this->respondSuccess([
            ['id' => 1, 'name' => 'Academic Buildings', 'slug' => 'academic-buildings', 'description' => 'University academic buildings and faculties', 'is_visible' => true, 'display_order' => 1, 'parent_id' => null],
            ['id' => 2, 'name' => 'Student Services', 'slug' => 'student-services', 'description' => 'Student support and administrative services', 'is_visible' => true, 'display_order' => 2, 'parent_id' => null],
            ['id' => 3, 'name' => 'Dining & Cafés', 'slug' => 'dining-cafes', 'description' => 'Restaurants and cafés on campus', 'is_visible' => true, 'display_order' => 3, 'parent_id' => null],
        ], 'Categories retrieved successfully');
    }

    public function store(StoreCategoryRequest $request)
    {
        $validated = $request->validated();

        return $this->respondSuccess([
            'id' => rand(100, 999),
            ...$validated,
            'slug' => $validated['slug'] ?? Str::slug($validated['name']),
            'created_at' => now()->toISOString(),
            'updated_at' => now()->toISOString(),
        ], 'Category created successfully', 201);
    }

    public function update(UpdateCategoryRequest $request, $id)
    {
        $validated = $request->validated();

        return $this->respondSuccess([
            'id' => (int)$id,
            ...$validated,
            'slug' => $validated['slug'] ?? null,
            'updated_at' => now()->toISOString(),
        ], 'Category updated successfully');
    }

    public function destroy($id)
    {
        return $this->respondSuccess(null, 'Category deleted successfully');
    }
}
