@extends('layouts.app')

@section('title', 'Categories')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Page Header -->
    <div class="text-center mb-12">
        <h1 class="text-4xl font-bold text-gray-900 mb-4">Shop by Category</h1>
        <p class="text-xl text-gray-600">Browse our wide selection of product categories</p>
    </div>

    <!-- Categories Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @foreach($categories as $category)
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition duration-300 group">
                <div class="h-48 bg-gradient-to-br from-indigo-400 to-purple-500 relative overflow-hidden">
                    <div class="absolute inset-0 bg-black bg-opacity-20 group-hover:bg-opacity-30 transition duration-300"></div>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <h3 class="text-2xl font-bold text-white text-center">{{ $category->name }}</h3>
                    </div>
                </div>
                <div class="p-6">
                    <p class="text-gray-600 mb-4">{{ $category->description }}</p>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500">
                            {{ $category->products()->active()->count() }} products
                        </span>
                        <a href="{{ route('categories.show', $category->slug) }}" 
                           class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition duration-300">
                            Browse Products
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    @if($categories->count() === 0)
        <div class="text-center py-12">
            <div class="text-gray-400 text-6xl mb-4">📂</div>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">No categories available</h3>
            <p class="text-gray-600">Check back later for new categories</p>
        </div>
    @endif
</div>
@endsection
