<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Categories') }}
            </h2>
            <a href="{{ route('categories.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Add Category
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Income Categories -->
                        <div>
                            <h3 class="text-lg font-semibold mb-4">Income Categories</h3>
                            <div class="space-y-4">
                                @forelse($categories->where('type', 'income') as $category)
                                    <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg">
                                        <div>
                                            <h4 class="font-medium">{{ $category->name }}</h4>
                                            @if($category->description)
                                                <p class="text-sm text-gray-600">{{ $category->description }}</p>
                                            @endif
                                        </div>
                                        <div class="flex space-x-2">
                                            <a href="{{ route('categories.edit', $category) }}" class="text-blue-600 hover:text-blue-800">
                                                Edit
                                            </a>
                                            <form action="{{ route('categories.destroy', $category) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-800" onclick="return confirm('Are you sure you want to delete this category?')">
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-gray-500">No income categories found.</p>
                                @endforelse
                            </div>
                        </div>

                        <!-- Expense Categories -->
                        <div>
                            <h3 class="text-lg font-semibold mb-4">Expense Categories</h3>
                            <div class="space-y-4">
                                @forelse($categories->where('type', 'expense') as $category)
                                    <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg">
                                        <div>
                                            <h4 class="font-medium">{{ $category->name }}</h4>
                                            @if($category->description)
                                                <p class="text-sm text-gray-600">{{ $category->description }}</p>
                                            @endif
                                        </div>
                                        <div class="flex space-x-2">
                                            <a href="{{ route('categories.edit', $category) }}" class="text-blue-600 hover:text-blue-800">
                                                Edit
                                            </a>
                                            <form action="{{ route('categories.destroy', $category) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-800" onclick="return confirm('Are you sure you want to delete this category?')">
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-gray-500">No expense categories found.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 