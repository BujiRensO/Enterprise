<?php

namespace Database\Seeders;

use App\Models\Transaction;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class TransactionSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();
        if (!$user) {
            $user = User::factory()->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);
        }

        $categories = Category::where('user_id', $user->id)->get();
        $incomeCategories = $categories->where('type', 'income');
        $expenseCategories = $categories->where('type', 'expense');

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
    }
} 