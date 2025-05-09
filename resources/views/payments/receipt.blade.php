<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Payment Receipt') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="max-w-2xl mx-auto">
                        <div class="text-center mb-8">
                            <h1 class="text-2xl font-bold text-gray-900">Payment Receipt</h1>
                            <p class="text-gray-600">Transaction ID: {{ $payment->transaction_id }}</p>
                        </div>

                        <div class="border-t border-gray-200 pt-6">
                            <dl class="divide-y divide-gray-200">
                                <div class="py-4 flex justify-between">
                                    <dt class="text-sm font-medium text-gray-500">Amount</dt>
                                    <dd class="text-sm text-gray-900">${{ number_format($payment->amount, 2) }}</dd>
                                </div>

                                <div class="py-4 flex justify-between">
                                    <dt class="text-sm font-medium text-gray-500">Payment Method</dt>
                                    <dd class="text-sm text-gray-900">{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</dd>
                                </div>

                                @if($payment->card_type)
                                <div class="py-4 flex justify-between">
                                    <dt class="text-sm font-medium text-gray-500">Card Type</dt>
                                    <dd class="text-sm text-gray-900">{{ ucfirst($payment->card_type) }}</dd>
                                </div>

                                <div class="py-4 flex justify-between">
                                    <dt class="text-sm font-medium text-gray-500">Last Four Digits</dt>
                                    <dd class="text-sm text-gray-900">{{ $payment->last_four_digits }}</dd>
                                </div>
                                @endif

                                <div class="py-4 flex justify-between">
                                    <dt class="text-sm font-medium text-gray-500">Date</dt>
                                    <dd class="text-sm text-gray-900">{{ $payment->processed_at->format('F j, Y H:i:s') }}</dd>
                                </div>

                                <div class="py-4 flex justify-between">
                                    <dt class="text-sm font-medium text-gray-500">Status</dt>
                                    <dd class="text-sm">
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                            Completed
                                        </span>
                                    </dd>
                                </div>
                            </dl>
                        </div>

                        <div class="mt-8 flex justify-center">
                            <button onclick="window.print()" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                                Print Receipt
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 