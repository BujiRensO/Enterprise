<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\OTP;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\PaymentReceipt;
use App\Mail\PaymentFailed;

class PaymentController extends Controller
{
    public function create()
    {
        return view('payments.create');
    }

    public function initiate(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:credit_card,debit_card,digital_wallet',
        ]);

        $payment = Payment::create([
            'user_id' => Auth::id(),
            'amount' => $validated['amount'],
            'payment_method' => $validated['payment_method'],
            'status' => 'pending',
        ]);

        return response()->json([
            'payment_id' => $payment->id,
            'message' => 'Payment initiated successfully',
        ]);
    }

    public function enterPaymentDetails(Request $request, Payment $payment)
    {
        $validated = $request->validate([
            'card_number' => 'required|string|size:16',
            'expiry_date' => 'required|string|size:5',
            'cvv' => 'required|string|size:3',
        ]);

        // Validate card type
        $cardType = $this->identifyCardType($validated['card_number']);
        if (!$cardType) {
            $payment->markAsFailed('Unsupported card type');
            return response()->json(['error' => 'Unsupported card type'], 400);
        }

        // Validate card
        if (!$this->validateCard($validated)) {
            $payment->markAsFailed('Card validation failed');
            return response()->json(['error' => 'Card validation failed'], 400);
        }

        // Update payment with card details
        $payment->update([
            'card_type' => $cardType,
            'last_four_digits' => substr($validated['card_number'], -4),
        ]);

        // Generate and send OTP
        $otp = OTP::generateForPayment($payment);
        
        // In a real application, you would send the OTP via SMS or email
        // For demo purposes, we'll return it in the response
        return response()->json([
            'message' => 'OTP sent successfully',
            'otp' => $otp->code, // Remove this in production
        ]);
    }

    public function verifyOTP(Request $request, Payment $payment)
    {
        $validated = $request->validate([
            'otp' => 'required|string|size:6',
        ]);

        $otp = $payment->otp;

        if (!$otp || !$otp->isValid()) {
            return response()->json(['error' => 'Invalid or expired OTP'], 400);
        }

        if ($otp->code !== $validated['otp']) {
            return response()->json(['error' => 'Incorrect OTP'], 400);
        }

        // Mark OTP as used
        $otp->markAsUsed();

        // Process payment
        try {
            $payment->markAsProcessing();
            
            // Simulate payment processing
            $success = $this->processPayment($payment);
            
            if ($success) {
                $transactionId = 'TXN' . time();
                $receiptUrl = route('payments.receipt', $payment);
                
                $payment->markAsCompleted($transactionId, $receiptUrl);
                
                // Send receipt email
                Mail::to(Auth::user()->email)->send(new PaymentReceipt($payment));
                
                return response()->json([
                    'message' => 'Payment completed successfully',
                    'receipt_url' => $receiptUrl,
                ]);
            } else {
                throw new \Exception('Payment processing failed');
            }
        } catch (\Exception $e) {
            $payment->markAsFailed($e->getMessage());
            Mail::to(Auth::user()->email)->send(new PaymentFailed($payment));
            
            return response()->json(['error' => 'Payment processing failed'], 500);
        }
    }

    public function receipt(Payment $payment)
    {
        if (!$payment->isCompleted()) {
            abort(404);
        }

        return view('payments.receipt', compact('payment'));
    }

    private function identifyCardType($cardNumber)
    {
        // Simple card type identification
        $firstDigit = substr($cardNumber, 0, 1);
        $firstTwoDigits = substr($cardNumber, 0, 2);

        if ($firstDigit === '4') {
            return 'visa';
        } elseif (in_array($firstTwoDigits, ['51', '52', '53', '54', '55'])) {
            return 'mastercard';
        } elseif (in_array($firstTwoDigits, ['34', '37'])) {
            return 'amex';
        }

        return null;
    }

    private function validateCard($cardDetails)
    {
        // Implement card validation logic
        // This is a simplified example
        return true;
    }

    private function processPayment($payment)
    {
        // Implement actual payment processing logic
        // This is a simplified example
        return true;
    }
}
