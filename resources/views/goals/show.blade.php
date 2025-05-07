<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $goal->name }}
            </h2>
            <div class="space-x-2">
                <a href="{{ route('goals.edit', $goal) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Edit Goal
                </a>
                <form action="{{ route('goals.destroy', $goal) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded" onclick="return confirm('Are you sure you want to delete this goal?')">
                        Delete Goal
                    </button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Goal Details -->
                        <div class="space-y-6">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900">Goal Details</h3>
                                <div class="mt-4 space-y-4">
                                    <div>
                                        <p class="text-sm text-gray-500">Description</p>
                                        <p class="mt-1">{{ $goal->description ?: 'No description provided' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500">Type</p>
                                        <p class="mt-1 capitalize">{{ str_replace('_', ' ', $goal->type) }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500">Status</p>
                                        <span class="mt-1 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $goal->status_color }}-100 text-{{ $goal->status_color }}-800">
                                            {{ ucfirst($goal->status) }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <h3 class="text-lg font-medium text-gray-900">Progress</h3>
                                <div class="mt-4 space-y-4">
                                    <div>
                                        <div class="flex justify-between text-sm text-gray-600 mb-1">
                                            <span>Progress</span>
                                            <span>{{ $goal->progress_percentage }}%</span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                                            <div class="{{ $goal->progress_bar_color }} h-2.5 rounded-full transition-all duration-500" style="width: {{ $goal->progress_percentage }}%"></div>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <p class="text-sm text-gray-500">Target Amount</p>
                                            <p class="font-semibold">${{ number_format($goal->target_amount, 2) }}</p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-500">Current Amount</p>
                                            <p class="font-semibold">${{ number_format($goal->current_amount, 2) }}</p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-500">Remaining Amount</p>
                                            <p class="font-semibold">${{ number_format($goal->remaining_amount, 2) }}</p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-500">Days Remaining</p>
                                            <p class="font-semibold">{{ $goal->days_remaining }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Update Progress -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">Update Progress</h3>
                            <form action="{{ route('goals.update-progress', $goal) }}" method="POST" class="mt-4 space-y-4">
                                @csrf
                                @method('PATCH')

                                <div>
                                    <x-input-label for="current_amount" :value="__('Current Amount')" />
                                    <x-text-input id="current_amount" name="current_amount" type="number" step="0.01" class="mt-1 block w-full" :value="$goal->current_amount" required />
                                    <x-input-error :messages="$errors->get('current_amount')" class="mt-2" />
                                </div>

                                <div class="flex items-center gap-4">
                                    <x-primary-button>{{ __('Update Progress') }}</x-primary-button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 