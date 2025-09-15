<?php

namespace App\Http\Controllers\API\Marketing;

use App\Http\Controllers\Controller;
use App\Models\CategorieProject;
use Illuminate\Http\Request;

class MarketingCategorieProject extends Controller
{
    //
    public function index()
    {
        $categories = CategorieProject::latest()->get();
        return response()->json($categories, 200);
    }

    // POST /api/supervisor/categories
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:project_categories,name',
            'description' => 'required|string',
        ]);

        $category = CategorieProject::create($validated);

        return response()->json([
            'message' => 'Category created successfully.',
            'data' => $category,
        ], 201);
    }

    // GET /api/supervisor/categories/{id}
    public function show(CategorieProject $category)
    {
        return response()->json($category, 200);
    }

    // PUT /api/supervisor/categories/{id}
    public function update(Request $request, CategorieProject $category)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255|unique:categorie_projects,name,' . $category->id,
            'description' => 'sometimes|string',
        ]);

        $category->update($validated);

        return response()->json([
            'message' => 'Category updated successfully.',
            'data' => $category,
        ], 200);
    }

    // DELETE /api/supervisor/categories/{id}
    public function destroy(CategorieProject $category)
    {
        $category->delete();

        return response()->json([
            'message' => 'Category deleted successfully.',
        ], 200);
    }
}
