@component('mail::message')
# Payment Failed

Dear {{ $payment->user->name }},

We're sorry, but your payment could not be processed.

**Transaction Details:**
- Payment ID: {{ $payment->id }}
- Amount: ${{ number_format($payment->amount, 2) }}
- Payment Method: {{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}
- Date: {{ $payment->created_at->format('F j, Y H:i:s') }}

**Error Message:**
{{ $payment->error_message }}

Please try again or contact our support team if the problem persists.

@component('mail::button', ['url' => route('payments.create')])
Try Again
@endcomponent

If you need assistance, please don't hesitate to contact our support team.

Best regards,<br>
{{ config('app.name') }}
@endcomponent 