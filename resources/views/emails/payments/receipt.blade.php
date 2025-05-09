@component('mail::message')
# Payment Receipt

Dear {{ $payment->user->name }},

Your payment has been successfully processed.

**Transaction Details:**
- Transaction ID: {{ $payment->transaction_id }}
- Amount: ${{ number_format($payment->amount, 2) }}
- Payment Method: {{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}
- Date: {{ $payment->processed_at->format('F j, Y H:i:s') }}

@if($payment->card_type)
**Card Details:**
- Card Type: {{ ucfirst($payment->card_type) }}
- Last Four Digits: {{ $payment->last_four_digits }}
@endif

@component('mail::button', ['url' => $payment->receipt_url])
View Receipt
@endcomponent

Thank you for your business!

Best regards,<br>
{{ config('app.name') }}
@endcomponent 