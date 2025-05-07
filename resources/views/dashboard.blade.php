<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if($transactions->isEmpty())
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-center">
                            <h3 class="text-xl font-medium text-gray-900 mb-4">Welcome to Your Financial Dashboard!</h3>
                            <p class="text-gray-600 mb-6">To get started quickly, you can add some sample data to your account. This will create:</p>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 max-w-2xl mx-auto mb-8 text-left">
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <h4 class="font-semibold text-gray-800 mb-2">Categories</h4>
                                    <ul class="list-disc list-inside text-gray-600">
                                        <li>Income categories (Salary, Freelance, etc.)</li>
                                        <li>Expense categories (Housing, Food, etc.)</li>
                                    </ul>
                                </div>
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <h4 class="font-semibold text-gray-800 mb-2">Transactions</h4>
                                    <ul class="list-disc list-inside text-gray-600">
                                        <li>3 months of sample transactions</li>
                                        <li>Realistic amounts for each category</li>
                                    </ul>
                                </div>
                            </div>

                            <a href="{{ route('seed.data') }}" 
                               class="inline-flex items-center px-6 py-3 bg-blue-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                                Add Sample Data
                            </a>

                            <p class="mt-4 text-sm text-gray-500">You can always add, edit, or delete data later.</p>
                        </div>
                    </div>
                </div>
            @else
                <!-- Summary Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <!-- Income Card -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 bg-white border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Monthly Income</h3>
                            <p class="mt-1 text-3xl font-semibold text-green-600">${{ number_format($monthlyIncome, 2) }}</p>
                        </div>
                    </div>
                    
                    <!-- Expense Card -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 bg-white border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Monthly Expenses</h3>
                            <p class="mt-1 text-3xl font-semibold text-red-600">${{ number_format($monthlyExpense, 2) }}</p>
                        </div>
                    </div>
                    
                    <!-- Balance Card -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 bg-white border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Net Balance</h3>
                            <p class="mt-1 text-3xl font-semibold {{ $netBalance >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                ${{ number_format($netBalance, 2) }}
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Expense by Category -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Expenses by Category</h3>
                        
                        @if($expensesByCategory->count() > 0)
                            <div class="relative overflow-x-auto">
                                <table class="w-full text-sm text-left text-gray-500">
                                    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-6 py-3">Category</th>
                                            <th scope="col" class="px-6 py-3">Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($expensesByCategory as $expense)
                                            <tr class="bg-white border-b">
                                                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                                    {{ $expense->name }}
                                                </th>
                                                <td class="px-6 py-4">${{ number_format($expense->total, 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-gray-500">No expenses recorded for this month.</p>
                        @endif
                    </div>
                </div>
                
                <!-- Quick Links -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Quick Actions</h3>
                        <div class="flex flex-wrap gap-4">
                            <a href="{{ route('transactions.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                                Add Transaction
                            </a>
                            <a href="{{ route('categories.index') }}" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                                Manage Categories
                            </a>
                            <a href="{{ route('budgets.index') }}" class="px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700">
                                Set Budgets
                            </a>
                            <a href="{{ route('reports.index') }}" class="px-4 py-2 bg-yellow-600 text-white rounded hover:bg-yellow-700">
                                View Reports
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>