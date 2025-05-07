<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Financial Goals') }}
            </h2>
            <a href="{{ route('goals.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Create New Goal
            </a>
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
                    @if($goals->isEmpty())
                        <div class="text-center py-8">
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No Financial Goals Yet</h3>
                            <p class="text-gray-500 mb-4">Start tracking your financial goals to achieve your dreams!</p>
                            <a href="{{ route('goals.create') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-400 to-blue-600 border border-transparent rounded-lg font-semibold text-sm text-gray-900 uppercase tracking-wider hover:from-blue-500 hover:to-blue-700 transform hover:scale-105 transition-all duration-200 shadow-lg hover:shadow-xl">
                                Create Your First Goal
                            </a>
                        </div>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($goals as $goal)
                                <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
                                    <div class="flex justify-between items-start mb-4">
                                        <h3 class="text-lg font-semibold text-gray-900">{{ $goal->name }}</h3>
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-{{ $goal->status_color }}-100 text-{{ $goal->status_color }}-800">
                                            {{ ucfirst($goal->status) }}
                                        </span>
                                    </div>
                                    
                                    <p class="text-gray-600 mb-4">{{ $goal->description }}</p>
                                    
                                    <div class="mb-4">
                                        <div class="flex justify-between text-sm text-gray-600 mb-1">
                                            <span>Progress</span>
                                            <span>{{ $goal->progress_percentage }}%</span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                                            <div class="{{ $goal->progress_bar_color }} h-2.5 rounded-full transition-all duration-500" style="width: {{ $goal->progress_percentage }}%"></div>
                                        </div>
                                    </div>
                                    
                                    <div class="grid grid-cols-2 gap-4 mb-4">
                                        <div>
                                            <p class="text-sm text-gray-500">Target Amount</p>
                                            <p class="font-semibold">${{ number_format($goal->target_amount, 2) }}</p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-500">Current Amount</p>
                                            <p class="font-semibold">${{ number_format($goal->current_amount, 2) }}</p>
                                        </div>
                                    </div>
                                    
                                    <div class="text-sm text-gray-500 mb-4">
                                        <p>Target Date: {{ $goal->target_date->format('M d, Y') }}</p>
                                        <p>Days Remaining: {{ $goal->days_remaining }}</p>
                                    </div>
                                    
                                    <div class="flex justify-between items-center">
                                        <a href="{{ route('goals.show', $goal) }}" class="text-blue-600 hover:text-blue-800">View Details</a>
                                        <div class="space-x-2">
                                            <a href="{{ route('goals.edit', $goal) }}" class="text-gray-600 hover:text-gray-800">Edit</a>
                                            <form action="{{ route('goals.destroy', $goal) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-800" onclick="return confirm('Are you sure you want to delete this goal?')">Delete</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 