<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Tambahkan style untuk hero pattern --}}
    <style>
        .hero-pattern {
            background-color: #1e40af;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='100' height='100' viewBox='0 0 100 100'%3E%3Cg fill-rule='evenodd'%3E%3Cg fill='%232563eb' fill-opacity='0.4'%3E%3Cpath opacity='.5' d='M96 95h4v1h-4v4h-1v-4h-9v4h-1v-4h-9v4h-1v-4h-9v4h-1v-4h-9v4h-1v-4h-9v4h-1v-4h-9v4h-1v-4h-9v4h-1v-4h-9v4h-1v-4H0v-1h15v-9H0v-1h15v-9H0v-1h15v-9H0v-1h15v-9H0v-1h15v-9H0v-1h15v-9H0v-1h15v-9H0v-1h15v-9H0v-1h15V0h1v15h9V0h1v15h9V0h1v15h9V0h1v15h9V0h1v15h9V0h1v15h9V0h1v15h9V0h1v15h4v1h-4v9h4v1h-4v9h4v1h-4v9h4v1h-4v9h4v1h-4v9h4v1h-4v9h4v1h-4v9h4v1h-4v9zm-1 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-9-10h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm9-10v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-9-10h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }
    </style>
    @livewireStyles
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-50">
        <!-- Navigation -->
        <nav class="fixed top-0 left-0 right-0 z-50 bg-white shadow">
            <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <!-- Logo -->
                    <div class="flex">
                        <div class="flex items-center flex-shrink-0">
                            <a href="{{ url('/') }}" class="text-2xl font-bold text-primary-600">
                                {{ config('app.name') }}
                            </a>
                        </div>

                        <!-- Navigation Links -->
                        <div class="hidden sm:ml-8 sm:flex sm:space-x-6">
                            <a href="{{ route('buses.index') }}" class="inline-flex items-center px-1 pt-1 text-sm font-medium text-gray-700 border-b-2 border-transparent hover:text-primary-600 hover:border-primary-600">
                                Buses
                            </a>
                            @auth
                                @if(Auth::user()->role === 'customer')
                                    <a href="{{ route('customer.bookings.index') }}" class="inline-flex items-center px-1 pt-1 text-sm font-medium text-gray-700 border-b-2 border-transparent hover:text-primary-600 hover:border-primary-600">
                                        My Bookings
                                    </a>
                                    <a href="{{ route('customer.reviews.index') }}" class="inline-flex items-center px-1 pt-1 text-sm font-medium text-gray-700 border-b-2 border-transparent hover:text-primary-600 hover:border-primary-600">
                                        My Reviews
                                    </a>
                                    <a href="{{ route('customer.crew-assignments.index') }}"
                                       class="inline-flex items-center px-1 pt-1 text-sm font-medium text-gray-700 border-b-2 border-transparent hover:text-primary-600 hover:border-primary-600">
                                        Assigned Crew
                                    </a>
                                @endif
                            @endauth
                        </div>
                    </div>

                    <!-- Right Navigation -->
                    <div class="flex items-center">
                        @auth
                            <div class="relative ml-3" x-data="{ open: false }">
                                <button @click="open = !open" class="flex items-center text-sm transition-colors bg-white rounded-full focus:outline-none focus:ring-2 focus:ring-primary-500">
                                    <div class="p-2 text-gray-600 hover:text-gray-900">
                                        {{ Auth::user()->name }}
                                        <svg class="inline w-4 h-4 ml-1 -mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </div>
                                </button>

                                <div x-show="open"
                                     @click.away="open = false"
                                     class="absolute right-0 z-50 w-48 py-1 mt-2 origin-top-right bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
                                     role="menu">
                                    @if(Auth::user()->role === 'customer')
                                        <a href="{{ route('customer.dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Dashboard</a>
                                        <a href="{{ route('customer.profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profile</a>
                                    @else
                                        <a href="{{ route('filament.panel.pages.dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Dashboard</a>
                                        <a href="{{ route('filament.panel.profile') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profile</a>
                                    @endif

                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="block w-full px-4 py-2 text-sm text-left text-gray-700 hover:bg-gray-100">
                                            Logout
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @else
                            <a href="{{ route('filament.panel.auth.login') }}" class="text-sm font-medium text-gray-700 hover:text-primary-600">Login</a>
                            <a href="{{ route('filament.panel.auth.register') }}" class="ml-4 text-sm font-medium text-white btn-primary">Register</a>
                        @endauth
                    </div>

                    <!-- Mobile menu button -->
                    <div class="flex items-center -mr-2 sm:hidden">
                        <button @click="mobileMenuOpen = !mobileMenuOpen" class="inline-flex items-center justify-center p-2 text-gray-400 rounded-md hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-primary-500">
                            <span class="sr-only">Open main menu</span>
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile menu -->
            <div x-show="mobileMenuOpen" class="sm:hidden">
                <div class="pt-2 pb-3 space-y-1">
                    <a href="{{ route('buses.index') }}" class="block py-2 pl-3 pr-4 text-base font-medium text-gray-700 hover:bg-gray-50">
                        Buses
                    </a>
                    @auth
                        @if(Auth::user()->role === 'customer')
                            <a href="{{ route('customer.bookings.index') }}" class="block py-2 pl-3 pr-4 text-base font-medium text-gray-700 hover:bg-gray-50">
                                My Bookings
                            </a>
                            <a href="{{ route('customer.reviews.index') }}" class="block py-2 pl-3 pr-4 text-base font-medium text-gray-700 hover:bg-gray-50">
                                My Reviews
                            </a>
                        @endif
                    @endauth
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <main>
            {{ $slot }}
        </main>
    </div>
    @livewireStyles
</body>
</html>
