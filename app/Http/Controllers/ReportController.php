<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::with('category')
            ->where('user_id', Auth::id());

        // Apply filters
        if ($request->filled('start_date')) {
            $query->where('date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->where('date', '<=', $request->end_date);
        }
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $transactions = $query->get();

        // Calculate summary
        $totalIncome = $transactions->where('type', 'income')->sum('amount');
        $totalExpenses = $transactions->where('type', 'expense')->sum('amount');
        $netBalance = $totalIncome - $totalExpenses;

        // Calculate category breakdown
        $categoryBreakdown = $transactions->groupBy('category_id')
            ->map(function ($items) {
                return [
                    'category' => $items->first()->category->name,
                    'type' => $items->first()->type,
                    'total' => $items->sum('amount'),
                    'percentage' => 0 // Will be calculated below
                ];
            });

        // Calculate percentages
        $totalAmount = $totalIncome + $totalExpenses;
        if ($totalAmount > 0) {
            $categoryBreakdown = $categoryBreakdown->map(function ($item) use ($totalAmount) {
                $item['percentage'] = ($item['total'] / $totalAmount) * 100;
                return $item;
            });
        }

        // Calculate monthly trends
        $monthlyTrends = $transactions->groupBy(function ($item) {
            return Carbon::parse($item->date)->format('Y-m');
        })->map(function ($items) {
            return [
                'income' => $items->where('type', 'income')->sum('amount'),
                'expenses' => $items->where('type', 'expense')->sum('amount'),
                'net' => $items->where('type', 'income')->sum('amount') - $items->where('type', 'expense')->sum('amount')
            ];
        })->sortKeys();

        $categories = Category::where('user_id', Auth::id())->get();

        return view('reports.index', compact(
            'transactions',
            'totalIncome',
            'totalExpenses',
            'netBalance',
            'categoryBreakdown',
            'monthlyTrends',
            'categories'
        ));
    }

    public function export(Request $request)
    {
        $query = Transaction::with('category')
            ->where('user_id', Auth::id());

        // Apply filters
        if ($request->filled('start_date')) {
            $query->where('date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->where('date', '<=', $request->end_date);
        }
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $transactions = $query->get();

        // Calculate summary
        $totalIncome = $transactions->where('type', 'income')->sum('amount');
        $totalExpenses = $transactions->where('type', 'expense')->sum('amount');
        $netBalance = $totalIncome - $totalExpenses;

        // Calculate category breakdown
        $categoryBreakdown = $transactions->groupBy('category_id')
            ->map(function ($items) {
                return [
                    'category' => $items->first()->category->name,
                    'type' => $items->first()->type,
                    'total' => $items->sum('amount'),
                    'percentage' => 0
                ];
            });

        // Calculate percentages
        $totalAmount = $totalIncome + $totalExpenses;
        if ($totalAmount > 0) {
            $categoryBreakdown = $categoryBreakdown->map(function ($item) use ($totalAmount) {
                $item['percentage'] = ($item['total'] / $totalAmount) * 100;
                return $item;
            });
        }

        // Calculate monthly trends
        $monthlyTrends = $transactions->groupBy(function ($item) {
            return Carbon::parse($item->date)->format('Y-m');
        })->map(function ($items) {
            return [
                'income' => $items->where('type', 'income')->sum('amount'),
                'expenses' => $items->where('type', 'expense')->sum('amount'),
                'net' => $items->where('type', 'income')->sum('amount') - $items->where('type', 'expense')->sum('amount')
            ];
        })->sortKeys();

        $data = [
            'totalIncome' => $totalIncome,
            'totalExpenses' => $totalExpenses,
            'netBalance' => $netBalance,
            'categoryBreakdown' => $categoryBreakdown,
            'monthlyTrends' => $monthlyTrends,
            'startDate' => $request->start_date,
            'endDate' => $request->end_date,
        ];

        $pdf = PDF::loadView('reports.export', $data);
        return $pdf->download('financial-report.pdf');
    }
}
