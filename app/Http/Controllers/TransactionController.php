<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Category;
use App\Models\Budget;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the transactions.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $query = Transaction::with('category')
            ->where('user_id', $user->id);
        
        // Filter by type if provided
        if ($request->has('type') && in_array($request->type, ['income', 'expense'])) {
            $query->where('type', $request->type);
        }
        
        // Filter by date range if provided
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('date', [$request->start_date, $request->end_date]);
        }
        
        // Filter by category if provided
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        
        $transactions = $query->orderBy('date', 'desc')->paginate(10);
        $categories = Category::where('user_id', $user->id)->get();
        
        return view('transactions.index', compact('transactions', 'categories'));
    }

    /**
     * Show the form for creating a new transaction.
     */
    public function create()
    {
        $user = Auth::user();
        $incomeCategories = Category::where('user_id', $user->id)
            ->where('type', 'income')
            ->get();
        $expenseCategories = Category::where('user_id', $user->id)
            ->where('type', 'expense')
            ->get();
            
        return view('transactions.create', compact('incomeCategories', 'expenseCategories'));
    }

    /**
     * Store a newly created transaction in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'type' => 'required|in:income,expense',
            'category_id' => 'required|exists:categories,id',
            'date' => 'required|date',
            'description' => 'nullable|string|max:255',
        ]);
        
        $user = Auth::user();
        
        // Create the transaction
        $transaction = new Transaction();
        $transaction->user_id = $user->id;
        $transaction->amount = $validated['amount'];
        $transaction->type = $validated['type'];
        $transaction->category_id = $validated['category_id'];
        $transaction->date = $validated['date'];
        $transaction->description = $validated['description'] ?? null;
        $transaction->save();
        
        // Check budget if it's an expense
        if ($validated['type'] == 'expense') {
            $this->checkBudgetLimits($user->id, $validated['category_id']);
        }
        
        return redirect()->route('transactions.index')
            ->with('success', 'Transaction created successfully.');
    }

    /**
     * Display the specified transaction.
     */
    public function show(Transaction $transaction)
    {
        $this->authorize('view', $transaction);
        
        return view('transactions.show', compact('transaction'));
    }

    /**
     * Show the form for editing the specified transaction.
     */
    public function edit(Transaction $transaction)
    {
        $this->authorize('update', $transaction);
        
        $user = Auth::user();
        $incomeCategories = Category::where('user_id', $user->id)
            ->where('type', 'income')
            ->get();
        $expenseCategories = Category::where('user_id', $user->id)
            ->where('type', 'expense')
            ->get();
            
        return view('transactions.edit', compact(
            'transaction', 
            'incomeCategories', 
            'expenseCategories'
        ));
    }

    /**
     * Update the specified transaction in storage.
     */
    public function update(Request $request, Transaction $transaction)
    {
        $this->authorize('update', $transaction);
        
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'type' => 'required|in:income,expense',
            'category_id' => 'required|exists:categories,id',
            'date' => 'required|date',
            'description' => 'nullable|string|max:255',
        ]);
        
        $oldType = $transaction->type;
        $oldCategoryId = $transaction->category_id;
        
        $transaction->amount = $validated['amount'];
        $transaction->type = $validated['type'];
        $transaction->category_id = $validated['category_id'];
        $transaction->date = $validated['date'];
        $transaction->description = $validated['description'] ?? null;
        $transaction->save();
        
        // Check budget if it's a new expense or updated expense
        if ($validated['type'] == 'expense') {
            $this->checkBudgetLimits(Auth::id(), $validated['category_id']);
        }
        
        // If it was previously an expense, check the old category's budget as well
        if ($oldType == 'expense' && $oldCategoryId != $validated['category_id']) {
            $this->checkBudgetLimits(Auth::id(), $oldCategoryId);
        }
        
        return redirect()->route('transactions.index')
            ->with('success', 'Transaction updated successfully.');
    }

    /**
     * Remove the specified transaction from storage.
     */
    public function destroy(Transaction $transaction)
    {
        $this->authorize('delete', $transaction);
        
        $categoryId = $transaction->category_id;
        $type = $transaction->type;
        
        $transaction->delete();
        
        // Check budget after deletion if it was an expense
        if ($type == 'expense') {
            $this->checkBudgetLimits(Auth::id(), $categoryId);
        }
        
        return redirect()->route('transactions.index')
            ->with('success', 'Transaction deleted successfully.');
    }
    
    /**
     * Check if budget limits have been exceeded for a category.
     */
    private function checkBudgetLimits($userId, $categoryId)
    {
        $budget = Budget::where('user_id', $userId)
            ->where('category_id', $categoryId)
            ->first();
            
        if (!$budget) {
            return;
        }
        
        $now = Carbon::now();
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
        
        // Get total expenses for this category in the current month
        $totalExpenses = Transaction::where('user_id', $userId)
            ->where('category_id', $categoryId)
            ->where('type', 'expense')
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->sum('amount');
            
        // If expenses exceed budget, flash a warning message
        if ($totalExpenses > $budget->amount) {
            session()->flash('warning', "Budget exceeded for category: {$budget->category->name}. Budget: $" . 
                number_format($budget->amount, 2) . ", Spent: $" . number_format($totalExpenses, 2));
        }
        // If expenses are close to budget (>80%), flash an info message
        elseif ($totalExpenses > ($budget->amount * 0.8)) {
            session()->flash('info', "Budget almost exceeded for category: {$budget->category->name}. Budget: $" . 
                number_format($budget->amount, 2) . ", Spent: $" . number_format($totalExpenses, 2) . 
                " (" . round(($totalExpenses / $budget->amount) * 100) . "%)");
        }
    }
}