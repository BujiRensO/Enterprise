<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Category;
use App\Models\Budget;
use Illuminate\Http\Request;
use Carbon\Carbon;
use PDF;

class ReportsController extends Controller
{
    public function index()
    {
        $months = collect(range(1, 12))->mapWithKeys(function ($month) {
            return [$month => Carbon::create()->month($month)->format('F')];
        });

        $years = collect(range(Carbon::now()->year - 2, Carbon::now()->year));

        return view('reports.index', compact('months', 'years'));
    }

    public function generate(Request $request)
    {
        $request->validate([
            'month' => 'required|integer|between:1,12',
            'year' => 'required|integer',
            'type' => 'required|in:summary,detailed',
        ]);

        $startDate = Carbon::createFromDate($request->year, $request->month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        $transactions = Transaction::with('category')
            ->where('user_id', auth()->id())
            ->whereBetween('date', [$startDate, $endDate])
            ->get();

        $totalIncome = $transactions->where('type', 'income')->sum('amount');
        $totalExpense = $transactions->where('type', 'expense')->sum('amount');
        $netBalance = $totalIncome - $totalExpense;

        $expensesByCategory = $transactions->where('type', 'expense')
            ->groupBy('category.name')
            ->map(function ($transactions) {
                return $transactions->sum('amount');
            });

        $budgets = Budget::with('category')
            ->where('user_id', auth()->id())
            ->where('month', $request->month)
            ->where('year', $request->year)
            ->get();

        $data = [
            'month' => Carbon::createFromDate(null, $request->month, 1)->format('F'),
            'year' => $request->year,
            'totalIncome' => $totalIncome,
            'totalExpense' => $totalExpense,
            'netBalance' => $netBalance,
            'expensesByCategory' => $expensesByCategory,
            'budgets' => $budgets,
            'transactions' => $transactions,
            'type' => $request->type,
            'user' => auth()->user(),
        ];

        $pdf = PDF::loadView('reports.pdf', $data);

        return $pdf->download("financial-report-{$request->month}-{$request->year}.pdf");
    }
} 