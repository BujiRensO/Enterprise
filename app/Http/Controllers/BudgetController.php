<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class BudgetController extends Controller
{
    /**
     * Display a listing of the budgets.
     */
    public function index()
    {
        $user = Auth::user();
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
        
        $budgets = Budget::with('category')
            ->where('user_id', $user->id)
            ->where('month', $currentMonth)
            ->where('year', $currentYear)
            ->get();
            
        $categories = Category::where('user_id', $user->id)
            ->where('type', 'expense')
            ->get();
            
        return view('budgets.index', compact('budgets', 'categories'));
    }

    /**
     * Show the form for creating a new budget.
     */
    public function create()
    {
        $user = Auth::user();
        $categories = Category::where('user_id', $user->id)
            ->where('type', 'expense')
            ->get();
            
        return view('budgets.create', compact('categories'));
    }

    /**
     * Store a newly created budget in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'amount' => 'required|numeric|min:0.01',
            'month' => 'required|integer|between:1,12',
            'year' => 'required|integer|min:2000',
        ]);
        
        // Check if budget already exists for this category and month/year
        $existingBudget = Budget::where('user_id', Auth::id())
            ->where('category_id', $validated['category_id'])
            ->where('month', $validated['month'])
            ->where('year', $validated['year'])
            ->first();
            
        if ($existingBudget) {
            return redirect()->route('budgets.create')
                ->with('error', 'A budget already exists for this category and month/year.');
        }
        
        $budget = new Budget();
        $budget->user_id = Auth::id();
        $budget->category_id = $validated['category_id'];
        $budget->amount = $validated['amount'];
        $budget->month = $validated['month'];
        $budget->year = $validated['year'];
        $budget->save();
        
        return redirect()->route('budgets.index')
            ->with('success', 'Budget created successfully.');
    }

    /**
     * Show the form for editing the specified budget.
     */
    public function edit(Budget $budget)
    {
        $this->authorize('update', $budget);
        
        $user = Auth::user();
        $categories = Category::where('user_id', $user->id)
            ->where('type', 'expense')
            ->get();
            
        return view('budgets.edit', compact('budget', 'categories'));
    }

    /**
     * Update the specified budget in storage.
     */
    public function update(Request $request, Budget $budget)
    {
        $this->authorize('update', $budget);
        
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'amount' => 'required|numeric|min:0.01',
            'month' => 'required|integer|between:1,12',
            'year' => 'required|integer|min:2000',
        ]);
        
        // Check if budget already exists for this category and month/year (excluding current budget)
        $existingBudget = Budget::where('user_id', Auth::id())
            ->where('category_id', $validated['category_id'])
            ->where('month', $validated['month'])
            ->where('year', $validated['year'])
            ->where('id', '!=', $budget->id)
            ->first();
            
        if ($existingBudget) {
            return redirect()->route('budgets.edit', $budget)
                ->with('error', 'A budget already exists for this category and month/year.');
        }
        
        $budget->category_id = $validated['category_id'];
        $budget->amount = $validated['amount'];
        $budget->month = $validated['month'];
        $budget->year = $validated['year'];
        $budget->save();
        
        return redirect()->route('budgets.index')
            ->with('success', 'Budget updated successfully.');
    }

    /**
     * Remove the specified budget from storage.
     */
    public function destroy(Budget $budget)
    {
        $this->authorize('delete', $budget);
        
        $budget->delete();
        
        return redirect()->route('budgets.index')
            ->with('success', 'Budget deleted successfully.');
    }
}
