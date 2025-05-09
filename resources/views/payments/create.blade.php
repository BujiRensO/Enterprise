<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Make Payment') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form id="payment-form" class="space-y-6">
                        @csrf
                        <div>
                            <label for="amount" class="block text-sm font-medium text-gray-700">Amount</label>
                            <div class="mt-1">
                                <input type="number" name="amount" id="amount" step="0.01" required
                                    class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                            </div>
                        </div>

                        <div>
                            <label for="payment_method" class="block text-sm font-medium text-gray-700">Payment Method</label>
                            <div class="mt-1">
                                <select name="payment_method" id="payment_method" required
                                    class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                    <option value="credit_card">Credit Card</option>
                                    <option value="debit_card">Debit Card</option>
                                    <option value="digital_wallet">Digital Wallet</option>
                                </select>
                            </div>
                        </div>

                        <div id="card-details" class="space-y-6 hidden">
                            <div>
                                <label for="card_number" class="block text-sm font-medium text-gray-700">Card Number</label>
                                <div class="mt-1">
                                    <input type="text" name="card_number" id="card_number" maxlength="16"
                                        class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="expiry_date" class="block text-sm font-medium text-gray-700">Expiry Date (MM/YY)</label>
                                    <div class="mt-1">
                                        <input type="text" name="expiry_date" id="expiry_date" maxlength="5"
                                            class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                    </div>
                                </div>

                                <div>
                                    <label for="cvv" class="block text-sm font-medium text-gray-700">CVV</label>
                                    <div class="mt-1">
                                        <input type="text" name="cvv" id="cvv" maxlength="3"
                                            class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="otp-section" class="space-y-6 hidden">
                            <div>
                                <label for="otp" class="block text-sm font-medium text-gray-700">Enter OTP</label>
                                <div class="mt-1">
                                    <input type="text" name="otp" id="otp" maxlength="6"
                                        class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" id="submit-button"
                                class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-400 to-blue-600 border border-transparent rounded-lg font-semibold text-sm text-gray-900 uppercase tracking-wider hover:from-blue-500 hover:to-blue-700 transform hover:scale-105 transition-all duration-200 shadow-lg hover:shadow-xl">
                                Continue
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Payment form script loaded');
            
            const form = document.getElementById('payment-form');
            const cardDetails = document.getElementById('card-details');
            const otpSection = document.getElementById('otp-section');
            const submitButton = document.getElementById('submit-button');
            let paymentId = null;

            const cardNumber = document.getElementById('card_number');
            const expiryDate = document.getElementById('expiry_date');
            const cvv = document.getElementById('cvv');
            const otpInput = document.getElementById('otp');

            document.getElementById('payment_method').addEventListener('change', function() {
                console.log('Payment method changed:', this.value);
                if (this.value === 'credit_card' || this.value === 'debit_card') {
                    cardDetails.classList.remove('hidden');
                    cardNumber.required = true;
                    expiryDate.required = true;
                    cvv.required = true;
                } else {
                    cardDetails.classList.add('hidden');
                    cardNumber.required = false;
                    expiryDate.required = false;
                    cvv.required = false;
                }
            });

            function showOtpSection() {
                otpSection.classList.remove('hidden');
                otpInput.required = true;
            }
            function hideOtpSection() {
                otpSection.classList.add('hidden');
                otpInput.required = false;
            }

            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                console.log('Form submitted');
                
                submitButton.disabled = true;
                submitButton.textContent = 'Processing...';

                try {
                    const formData = new FormData(form);
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

                    if (!paymentId) {
                        console.log('Initiating payment...');
                        // Step 1: Initiate payment
                        const response = await fetch('{{ route("payments.initiate") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                amount: formData.get('amount'),
                                payment_method: formData.get('payment_method')
                            })
                        });

                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }

                        console.log('Response status:', response.status);
                        const data = await response.json();
                        console.log('Response data:', data);

                        if (data.payment_id) {
                            paymentId = data.payment_id;
                            cardDetails.classList.remove('hidden');
                            submitButton.textContent = 'Submit Payment Details';
                        } else {
                            throw new Error(data.message || 'Failed to initiate payment');
                        }
                    } else if (!otpSection.classList.contains('hidden')) {
                        console.log('Verifying OTP...');
                        // Step 3: Verify OTP
                        const response = await fetch(`/payments/${paymentId}/verify-otp`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                otp: formData.get('otp')
                            })
                        });

                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }

                        console.log('Response status:', response.status);
                        const data = await response.json();
                        console.log('Response data:', data);

                        if (data.receipt_url) {
                            window.location.href = data.receipt_url;
                        } else {
                            throw new Error(data.message || 'Failed to verify OTP');
                        }
                    } else {
                        console.log('Submitting payment details...');
                        // Step 2: Submit payment details
                        const response = await fetch(`/payments/${paymentId}/details`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                card_number: formData.get('card_number'),
                                expiry_date: formData.get('expiry_date'),
                                cvv: formData.get('cvv')
                            })
                        });

                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }

                        console.log('Response status:', response.status);
                        const data = await response.json();
                        console.log('Response data:', data);

                        if (data.otp) {
                            showOtpSection();
                            submitButton.textContent = 'Verify OTP';
                            alert(`Your OTP is: ${data.otp}`); // In production, this would be sent via SMS/email
                        } else {
                            throw new Error(data.message || 'Failed to process payment details');
                        }
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert(error.message || 'An error occurred. Please try again.');
                } finally {
                    submitButton.disabled = false;
                }
            });
        });
    </script>
    @endpush
</x-app-layout> 