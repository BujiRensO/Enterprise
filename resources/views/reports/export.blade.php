<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Financial Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #333;
            margin: 0;
        }
        .header p {
            color: #666;
            margin: 5px 0 0;
        }
        .summary {
            margin-bottom: 30px;
        }
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 20px;
        }
        .summary-card {
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 5px;
        }
        .summary-card h3 {
            margin: 0 0 10px;
            color: #333;
            font-size: 16px;
        }
        .summary-card p {
            margin: 0;
            font-size: 20px;
            font-weight: bold;
        }
        .income { color: #059669; }
        .expense { color: #dc2626; }
        .net-positive { color: #059669; }
        .net-negative { color: #dc2626; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f3f4f6;
            font-weight: bold;
        }
        .section {
            margin-bottom: 30px;
        }
        .section h2 {
            color: #333;
            margin: 0 0 15px;
            font-size: 18px;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            color: #666;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Financial Report</h1>
        <p>Generated on {{ now()->format('F j, Y') }}</p>
        @if(request('start_date') && request('end_date'))
            <p>Period: {{ \Carbon\Carbon::parse(request('start_date'))->format('F j, Y') }} - {{ \Carbon\Carbon::parse(request('end_date'))->format('F j, Y') }}</p>
        @endif
    </div>

    <div class="summary">
        <div class="summary-grid">
            <div class="summary-card">
                <h3>Total Income</h3>
                <p class="income">${{ number_format($totalIncome, 2) }}</p>
            </div>
            <div class="summary-card">
                <h3>Total Expenses</h3>
                <p class="expense">${{ number_format($totalExpenses, 2) }}</p>
            </div>
            <div class="summary-card">
                <h3>Net Balance</h3>
                <p class="{{ $netBalance >= 0 ? 'net-positive' : 'net-negative' }}">
                    ${{ number_format($netBalance, 2) }}
                </p>
            </div>
        </div>
    </div>

    <div class="section">
        <h2>Category Breakdown</h2>
        <table>
            <thead>
                <tr>
                    <th>Category</th>
                    <th>Type</th>
                    <th>Total Amount</th>
                    <th>Percentage</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categoryBreakdown as $breakdown)
                    <tr>
                        <td>{{ $breakdown->category_name }}</td>
                        <td>{{ ucfirst($breakdown->type) }}</td>
                        <td>${{ number_format($breakdown->total_amount, 2) }}</td>
                        <td>{{ number_format($breakdown->percentage, 1) }}%</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="text-align: center;">No data available.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="section">
        <h2>Monthly Trend</h2>
        <table>
            <thead>
                <tr>
                    <th>Month</th>
                    <th>Income</th>
                    <th>Expenses</th>
                    <th>Net</th>
                </tr>
            </thead>
            <tbody>
                @forelse($monthlyTrend as $trend)
                    <tr>
                        <td>{{ date('F Y', mktime(0, 0, 0, $trend->month, 1, $trend->year)) }}</td>
                        <td class="income">${{ number_format($trend->income, 2) }}</td>
                        <td class="expense">${{ number_format($trend->expenses, 2) }}</td>
                        <td class="{{ $trend->net >= 0 ? 'net-positive' : 'net-negative' }}">
                            ${{ number_format($trend->net, 2) }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="text-align: center;">No data available.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="footer">
        <p>This report was generated by the Financial Management System.</p>
        <p>© {{ date('Y') }} Financial Management System. All rights reserved.</p>
    </div>
</body>
</html>