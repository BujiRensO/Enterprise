<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    /**
     * Display a listing of the categories.
     */
    public function index()
    {
        $user = Auth::user();
        $categories = Category::where('user_id', $user->id)
            ->orderBy('type')
            ->orderBy('name')
            ->get();
            
        return view('categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new category.
     */
    public function create()
    {
        return view('categories.create');
    }

    /**
     * Store a newly created category in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:income,expense',
            'description' => 'nullable|string|max:255',
        ]);
        
        $category = new Category();
        $category->user_id = Auth::id();
        $category->name = $validated['name'];
        $category->type = $validated['type'];
        $category->description = $validated['description'] ?? null;
        $category->save();
        
        return redirect()->route('categories.index')
            ->with('success', 'Category created successfully.');
    }

    /**
     * Show the form for editing the specified category.
     */
    public function edit(Category $category)
    {
        $this->authorize('update', $category);
        return view('categories.edit', compact('category'));
    }

    /**
     * Update the specified category in storage.
     */
    public function update(Request $request, Category $category)
    {
        $this->authorize('update', $category);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:income,expense',
            'description' => 'nullable|string|max:255',
        ]);
        
        $category->name = $validated['name'];
        $category->type = $validated['type'];
        $category->description = $validated['description'] ?? null;
        $category->save();
        
        return redirect()->route('categories.index')
            ->with('success', 'Category updated successfully.');
    }

    /**
     * Remove the specified category from storage.
     */
    public function destroy(Category $category)
    {
        $this->authorize('delete', $category);
        
        // Check if category has transactions
        if ($category->transactions()->exists()) {
            return redirect()->route('categories.index')
                ->with('error', 'Cannot delete category with existing transactions.');
        }
        
        $category->delete();
        
        return redirect()->route('categories.index')
            ->with('success', 'Category deleted successfully.');
    }
}
