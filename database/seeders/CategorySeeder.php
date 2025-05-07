<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
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
            Category::create([
                'user_id' => $user->id,
                'name' => $category['name'],
                'type' => $category['type'],
                'description' => $category['description'],
            ]);
        }
    }
} 