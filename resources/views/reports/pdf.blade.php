<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Financial Report - {{ $month }} {{ $year }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #eee;
            padding-bottom: 20px;
        }
        .user-info {
            text-align: right;
            margin-bottom: 20px;
            font-size: 14px;
            color: #666;
        }
        .summary {
            margin-bottom: 30px;
        }
        .summary-item {
            margin-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f5f5f5;
        }
        .positive {
            color: #059669;
        }
        .negative {
            color: #dc2626;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #eee;
            padding-top: 20px;
        }
    </style>
</head>
<body>
    <div class="user-info">
        <strong>Report Owner:</strong> {{ $user->name }}<br>
        <strong>Email:</strong> {{ $user->email }}
    </div>

    <div class="header">
        <h1>Financial Report</h1>
        <h2>{{ $month }} {{ $year }}</h2>
    </div>

    <div class="summary">
        <h3>Summary</h3>
        <div class="summary-item">
            <strong>Total Income:</strong> 
            <span class="positive">${{ number_format($totalIncome, 2) }}</span>
        </div>
        <div class="summary-item">
            <strong>Total Expenses:</strong> 
            <span class="negative">${{ number_format($totalExpense, 2) }}</span>
        </div>
        <div class="summary-item">
            <strong>Net Balance:</strong> 
            <span class="{{ $netBalance >= 0 ? 'positive' : 'negative' }}">
                ${{ number_format($netBalance, 2) }}
            </span>
        </div>
    </div>

    <div class="expenses">
        <h3>Expenses by Category</h3>
        <table>
            <thead>
                <tr>
                    <th>Category</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($expensesByCategory as $category => $amount)
                    <tr>
                        <td>{{ $category }}</td>
                        <td class="negative">${{ number_format($amount, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if($type === 'detailed')
        <div class="transactions">
            <h3>Transaction Details</h3>
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Category</th>
                        <th>Type</th>
                        <th>Description</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transactions as $transaction)
                        <tr>
                            <td>{{ $transaction->date->format('Y-m-d') }}</td>
                            <td>{{ $transaction->category->name }}</td>
                            <td>{{ ucfirst($transaction->type) }}</td>
                            <td>{{ $transaction->description }}</td>
                            <td class="{{ $transaction->type === 'income' ? 'positive' : 'negative' }}">
                                ${{ number_format($transaction->amount, 2) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="budgets">
            <h3>Budget Status</h3>
            <table>
                <thead>
                    <tr>
                        <th>Category</th>
                        <th>Budget</th>
                        <th>Spent</th>
                        <th>Remaining</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($budgets as $budget)
                        @php
                            $spent = $expensesByCategory[$budget->category->name] ?? 0;
                            $remaining = $budget->amount - $spent;
                        @endphp
                        <tr>
                            <td>{{ $budget->category->name }}</td>
                            <td>${{ number_format($budget->amount, 2) }}</td>
                            <td class="negative">${{ number_format($spent, 2) }}</td>
                            <td class="{{ $remaining >= 0 ? 'positive' : 'negative' }}">
                                ${{ number_format($remaining, 2) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <div class="footer">
        <p>Generated on {{ now()->format('Y-m-d H:i:s') }}</p>
    </div>
</body>
</html> 