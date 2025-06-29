<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Performance optimizations -->
    <meta name="theme-color" content="#4f46e5">
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link rel="preconnect" href="https://fonts.bunny.net" crossorigin>

    <title>{{ config('app.name', 'QuickShop') }} - @yield('title', 'E-Commerce')</title>

    <!-- Meta description for SEO -->
    <meta name="description" content="@yield('description', 'Quick Shop - Your trusted online store for quality products at great prices.')">

    <!-- Fonts with display=swap for better performance -->
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-gray-50">
    <div class="min-h-screen">
        <!-- Navigation -->
        <nav class="bg-white shadow-sm border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <!-- Logo -->
                    <div class="flex items-center">
                        <a href="{{ route('home') }}" class="flex-shrink-0">
                            <h1 class="text-2xl font-bold text-indigo-600">QuickShop</h1>
                        </a>
                    </div>

                    <!-- Navigation Links -->
                    <div class="hidden md:flex items-center space-x-8">
                        <a href="{{ route('home') }}"
                            class="text-gray-700 hover:text-indigo-600 px-3 py-2 text-sm font-medium">
                            Home
                        </a>
                        <a href="{{ route('shop.index') }}"
                            class="text-gray-700 hover:text-indigo-600 px-3 py-2 text-sm font-medium">
                            Shop
                        </a>
                        <a href="{{ route('categories.index') }}"
                            class="text-gray-700 hover:text-indigo-600 px-3 py-2 text-sm font-medium">
                            Categories
                        </a>
                    </div>

                    <!-- Right Side -->
                    <div class="flex items-center space-x-4">
                        <!-- Cart -->
                        <a href="{{ route('cart.index') }}" class="relative text-gray-700 hover:text-indigo-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.5 6M7 13l-1.5 6m0 0h9m-9 0h9"></path>
                            </svg>
                            @if (session('cart_count', 0) > 0)
                                <span
                                    class="absolute -top-2 -right-2 bg-indigo-600 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                                    {{ session('cart_count', 0) }}
                                </span>
                            @endif
                        </a>

                        <!-- Authentication -->
                        @auth
                            <div class="relative">
                                <button type="button" class="flex items-center text-sm text-gray-700 hover:text-indigo-600"
                                    id="user-menu-button">
                                    <span>{{ Auth::user()->name }}</span>
                                    <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                                <div class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 hidden"
                                    id="user-menu">
                                    <a href="{{ route('account.orders') }}"
                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">My Orders</a>
                                    <a href="{{ route('account.profile') }}"
                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profile</a>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit"
                                            class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            Logout
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @else
                            <a href="{{ route('login') }}"
                                class="text-gray-700 hover:text-indigo-600 px-3 py-2 text-sm font-medium">
                                Login
                            </a>
                            <a href="{{ route('register') }}"
                                class="bg-indigo-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-indigo-700">
                                Register
                            </a>
                        @endauth
                    </div>

                    <!-- Mobile menu button -->
                    <div class="md:hidden flex items-center">
                        <button type="button" class="text-gray-700 hover:text-indigo-600" id="mobile-menu-button">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile menu -->
            <div class="md:hidden hidden" id="mobile-menu">
                <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3 bg-white border-t border-gray-200">
                    <a href="{{ route('home') }}" class="block px-3 py-2 text-gray-700 hover:text-indigo-600">Home</a>
                    <a href="{{ route('shop.index') }}"
                        class="block px-3 py-2 text-gray-700 hover:text-indigo-600">Shop</a>
                    <a href="{{ route('categories.index') }}"
                        class="block px-3 py-2 text-gray-700 hover:text-indigo-600">Categories</a>
                    @guest
                        <a href="{{ route('login') }}"
                            class="block px-3 py-2 text-gray-700 hover:text-indigo-600">Login</a>
                        <a href="{{ route('register') }}"
                            class="block px-3 py-2 text-gray-700 hover:text-indigo-600">Register</a>
                    @endguest
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <main>
            @if (session('success'))
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4">
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                        {{ session('success') }}
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4">
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        {{ session('error') }}
                    </div>
                </div>
            @endif

            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="bg-gray-800 text-white mt-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                    <div>
                        <h3 class="text-lg font-semibold mb-4">QuickShop</h3>
                        <p class="text-gray-300">Your one-stop shop for everything you need.</p>
                    </div>
                    <div>
                        <h4 class="text-md font-semibold mb-4">Quick Links</h4>
                        <ul class="space-y-2">
                            <li><a href="{{ route('home') }}" class="text-gray-300 hover:text-white">Home</a></li>
                            <li><a href="{{ route('shop.index') }}" class="text-gray-300 hover:text-white">Shop</a>
                            </li>
                            <li><a href="{{ route('categories.index') }}"
                                    class="text-gray-300 hover:text-white">Categories</a></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="text-md font-semibold mb-4">Customer Service</h4>
                        <ul class="space-y-2">
                            <li><a href="#" class="text-gray-300 hover:text-white">Contact Us</a></li>
                            <li><a href="#" class="text-gray-300 hover:text-white">FAQ</a></li>
                            <li><a href="#" class="text-gray-300 hover:text-white">Shipping Info</a></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="text-md font-semibold mb-4">Follow Us</h4>
                        <div class="flex space-x-4">
                            <a href="#" class="text-gray-300 hover:text-white">Facebook</a>
                            <a href="#" class="text-gray-300 hover:text-white">Twitter</a>
                            <a href="#" class="text-gray-300 hover:text-white">Instagram</a>
                        </div>
                    </div>
                </div>
                <div class="border-t border-gray-700 mt-8 pt-8 text-center">
                    <p class="text-gray-300">&copy; {{ date('Y') }} QuickShop. All rights reserved.</p>
                </div>
            </div>
        </footer>
    </div>

    <script>
        // Mobile menu toggle
        document.getElementById('mobile-menu-button').addEventListener('click', function() {
            document.getElementById('mobile-menu').classList.toggle('hidden');
        });

        // User menu toggle
        const userMenuButton = document.getElementById('user-menu-button');
        const userMenu = document.getElementById('user-menu');

        if (userMenuButton && userMenu) {
            userMenuButton.addEventListener('click', function() {
                userMenu.classList.toggle('hidden');
            });

            // Close menu when clicking outside
            document.addEventListener('click', function(event) {
                if (!userMenuButton.contains(event.target) && !userMenu.contains(event.target)) {
                    userMenu.classList.add('hidden');
                }
            });
        }
    </script>
</body>

</html>
