<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
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
        </div>
    </div>
</x-app-layout>