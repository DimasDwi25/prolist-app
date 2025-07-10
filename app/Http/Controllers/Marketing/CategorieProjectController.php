<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Models\CategorieProject;
use Illuminate\Http\Request;

class CategorieProjectController extends Controller
{
    //
    public function index()
    {
        $categories = CategorieProject::latest()->get();
        return view('marketing.categorie_project.index', compact('categories'));
    }

    public function create()
    {
        return view('marketing.categorie_project.form');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        CategorieProject::create($request->only('name', 'description'));

        return redirect()->route('marketing.category')->with('success', 'Category created successfully.');
    }

    public function edit(CategorieProject $category)
    {
        return view('marketing.categorie_project.form', compact('category'));
    }

    public function update(Request $request, CategorieProject $category)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        $category->update($request->only('name', 'description'));

        return redirect()->route('marketing.category')->with('success', 'Category updated successfully.');
    }

    public function destroy(CategorieProject $category)
    {
        $category->delete();
        return redirect()->route('marketing.category')->with('success', 'Category deleted successfully.');
    }
}
