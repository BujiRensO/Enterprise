<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SampleDataController extends Controller
{
    public function seed()
    {
        $user = auth()->user();

        // Create categories if they don't exist
        $categories = [
            // Income Categories
            ['name' => 'Salary', 'type' => 'income', 'description' => 'Monthly salary income'],
            ['name' => 'Freelance', 'type' => 'income', 'description' => 'Freelance work income'],
            ['name' => 'Investments', 'type' => 'income', 'description' => 'Investment returns'],
            ['name' => 'Gifts', 'type' => 'income', 'description' => 'Gift money received'],
            
            // Expense Categories
            ['name' => 'Housing', 'type' => 'expense', 'description' => 'Rent, mortgage, utilities'],
            ['name' => 'Food', 'type' => 'expense', 'description' => 'Groceries and dining out'],
            ['name' => 'Transportation', 'type' => 'expense', 'description' => 'Gas, public transport, car maintenance'],
            ['name' => 'Entertainment', 'type' => 'expense', 'description' => 'Movies, games, hobbies'],
            ['name' => 'Shopping', 'type' => 'expense', 'description' => 'Clothes, electronics, etc.'],
            ['name' => 'Healthcare', 'type' => 'expense', 'description' => 'Medical expenses and insurance'],
            ['name' => 'Education', 'type' => 'expense', 'description' => 'Tuition, books, courses'],
            ['name' => 'Savings', 'type' => 'expense', 'description' => 'Money put into savings'],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'name' => $category['name'],
                    'type' => $category['type']
                ],
                [
                    'description' => $category['description']
                ]
            );
        }

        // Get the created categories
        $userCategories = Category::where('user_id', $user->id)->get();
        $incomeCategories = $userCategories->where('type', 'income');
        $expenseCategories = $userCategories->where('type', 'expense');

        // Generate transactions for the last 3 months
        for ($i = 0; $i < 3; $i++) {
            $date = Carbon::now()->subMonths($i);

            // Add income transactions
            foreach ($incomeCategories as $category) {
                $amount = match($category->name) {
                    'Salary' => rand(3000, 5000),
                    'Freelance' => rand(500, 2000),
                    'Investments' => rand(100, 500),
                    'Gifts' => rand(50, 200),
                    default => rand(100, 1000),
                };

                Transaction::create([
                    'user_id' => $user->id,
                    'category_id' => $category->id,
                    'amount' => $amount,
                    'type' => 'income',
                    'date' => $date->copy()->day(rand(1, 5)),
                    'description' => "Monthly {$category->name}",
                ]);
            }

            // Add expense transactions
            foreach ($expenseCategories as $category) {
                $amount = match($category->name) {
                    'Housing' => rand(1000, 2000),
                    'Food' => rand(300, 800),
                    'Transportation' => rand(100, 300),
                    'Entertainment' => rand(50, 200),
                    'Shopping' => rand(100, 500),
                    'Healthcare' => rand(50, 300),
                    'Education' => rand(200, 1000),
                    'Savings' => rand(500, 1000),
                    default => rand(50, 200),
                };

                // Create 2-3 transactions per category per month
                $numTransactions = rand(2, 3);
                for ($j = 0; $j < $numTransactions; $j++) {
                    Transaction::create([
                        'user_id' => $user->id,
                        'category_id' => $category->id,
                        'amount' => $amount / $numTransactions,
                        'type' => 'expense',
                        'date' => $date->copy()->day(rand(1, 28)),
                        'description' => "{$category->name} expense",
                    ]);
                }
            }
        }

        return redirect()->back()->with('success', 'Sample data has been added to your account successfully!');
    }
} 