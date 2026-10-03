<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('manage-master-data');
        $query = Category::latest();
        if ($request->has('search') && $request->search != '') {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        $categories = $query->paginate(10)->withQueryString();
        return view('categories.index', compact('categories'));
    }

    public function create()
    {
        Gate::authorize('manage-master-data');
        return view('categories.create');
    }

    public function store(Request $request)
    {
        Gate::authorize('manage-master-data');
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $category = Category::create($validated);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'category' => $category]);
        }

        return redirect()->route('categories.index')->with('success', 'Category created successfully.');
    }

    public function edit(Category $category)
    {
        Gate::authorize('manage-master-data');
        return view('categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        Gate::authorize('manage-master-data');
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $category->update($validated);

        return redirect()->route('categories.index')->with('success', 'Category updated successfully.');
    }

    public function destroy(Category $category)
    {
        Gate::authorize('delete-master-data');
        if ($category->items()->count() > 0) {
            return redirect()->route('categories.index')->with('error', 'Cannot delete category because it has associated items.');
        }
        $category->delete();
        return redirect()->route('categories.index')->with('success', 'Category deleted successfully.');
    }
}
