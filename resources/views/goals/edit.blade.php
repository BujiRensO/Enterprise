<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Financial Goal') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form action="{{ route('goals.update', $goal) }}" method="POST" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div>
                            <x-input-label for="name" :value="__('Goal Name')" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $goal->name)" required />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="description" :value="__('Description')" />
                            <textarea id="description" name="description" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" rows="3">{{ old('description', $goal->description) }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="type" :value="__('Goal Type')" />
                            <select id="type" name="type" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="savings" {{ old('type', $goal->type) == 'savings' ? 'selected' : '' }}>Savings</option>
                                <option value="debt_payoff" {{ old('type', $goal->type) == 'debt_payoff' ? 'selected' : '' }}>Debt Payoff</option>
                                <option value="purchase" {{ old('type', $goal->type) == 'purchase' ? 'selected' : '' }}>Purchase</option>
                                <option value="other" {{ old('type', $goal->type) == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                            <x-input-error :messages="$errors->get('type')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="target_amount" :value="__('Target Amount')" />
                            <x-text-input id="target_amount" name="target_amount" type="number" step="0.01" class="mt-1 block w-full" :value="old('target_amount', $goal->target_amount)" required />
                            <x-input-error :messages="$errors->get('target_amount')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="current_amount" :value="__('Current Amount')" />
                            <x-text-input id="current_amount" name="current_amount" type="number" step="0.01" class="mt-1 block w-full" :value="old('current_amount', $goal->current_amount)" required />
                            <x-input-error :messages="$errors->get('current_amount')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="target_date" :value="__('Target Date')" />
                            <x-text-input id="target_date" name="target_date" type="date" class="mt-1 block w-full" :value="old('target_date', $goal->target_date->format('Y-m-d'))" required />
                            <x-input-error :messages="$errors->get('target_date')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="status" :value="__('Status')" />
                            <select id="status" name="status" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="active" {{ old('status', $goal->status) == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="completed" {{ old('status', $goal->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="cancelled" {{ old('status', $goal->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                            <x-input-error :messages="$errors->get('status')" class="mt-2" />
                        </div>

                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('Update Goal') }}</x-primary-button>
                            <a href="{{ route('goals.show', $goal) }}" class="text-gray-600 hover:text-gray-900">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 