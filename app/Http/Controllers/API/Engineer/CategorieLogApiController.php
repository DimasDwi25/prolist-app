<?php

namespace App\Http\Controllers\API\Engineer;

use App\Http\Controllers\Controller;
use App\Models\CategorieLog;
use Illuminate\Http\Request;

class CategorieLogApiController extends Controller
{
    /**
     * Tampilkan semua kategori log
     */
    public function index()
    {
        $categories = CategorieLog::all();
        return response()->json($categories);
    }

    /**
     * Simpan kategori log baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categorie_logs,name',
        ]);

        $category = CategorieLog::create($validated);

        return response()->json($category, 201);
    }

    /**
     * Tampilkan kategori log tertentu
     */
    public function show($id)
    {
        $category = CategorieLog::find($id);
        if (!$category) {
            return response()->json(['message' => 'Kategori tidak ditemukan'], 404);
        }
        return response()->json($category);
    }

    /**
     * Update kategori log
     */
    public function update(Request $request, $id)
    {
        $category = CategorieLog::find($id);
        if (!$category) {
            return response()->json(['message' => 'Kategori tidak ditemukan'], 404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categorie_logs,name,' . $id,
        ]);

        $category->update($validated);

        return response()->json($category);
    }

    /**
     * Hapus kategori log
     */
    public function destroy($id)
    {
        $category = CategorieLog::find($id);
        if (!$category) {
            return response()->json(['message' => 'Kategori tidak ditemukan'], 404);
        }

        $category->delete();

        return response()->json(['message' => 'Kategori berhasil dihapus']);
    }
}
