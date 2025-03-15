<x-app-layout>
    <div class="py-12 mt-5">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="overflow-hidden">
                {{-- Profile Header --}}
                <div class="p-8 bg-white shadow-sm rounded-xl">
                    <div class="flex items-center gap-6">
                        {{-- Avatar --}}
                        <div class="flex-shrink-0">
                            <div class="relative">
                                <div class="flex items-center justify-center w-24 h-24 rounded-full bg-primary-100">
                                    <span class="text-3xl font-medium text-primary-600">
                                        {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                                    </span>
                                </div>
                                <button type="button" class="absolute bottom-0 right-0 p-1.5 rounded-full bg-white border border-gray-200 shadow-sm hover:bg-gray-50">
                                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">{{ auth()->user()->name }}</h1>
                            <p class="text-gray-500">Member since {{ auth()->user()->created_at->format('F Y') }}</p>
                        </div>
                    </div>
                </div>

                {{-- Profile Form --}}
                <div class="mt-6 bg-white shadow-sm rounded-xl">
                    <div class="p-8">
                        <form method="post" action="{{ route('customer.profile.update') }}">
                            @csrf
                            @method('patch')

                            <div class="space-y-8">
                                {{-- Personal Information Section --}}
                                <div>
                                    <h2 class="text-lg font-semibold text-gray-900">Personal Information</h2>
                                    <p class="mt-1 text-sm text-gray-500">Update your personal information and contact details.</p>

                                    <div class="grid gap-6 mt-6 md:grid-cols-2">
                                        {{-- Name --}}
                                        <div>
                                            <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
                                            <input type="text" name="name" id="name"
                                                value="{{ old('name', auth()->user()->name) }}"
                                                class="mt-1 form-input" required>
                                            @error('name')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        {{-- Email --}}
                                        <div>
                                            <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                                            <input type="email" name="email" id="email"
                                                value="{{ old('email', auth()->user()->email) }}"
                                                class="mt-1 form-input" required>
                                            @error('email')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        {{-- Phone --}}
                                        <div>
                                            <label for="phone" class="block text-sm font-medium text-gray-700">Phone Number</label>
                                            <div class="relative mt-1">
                                                <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                                                    <span class="text-gray-500">+62</span>
                                                </div>
                                                <input type="tel" name="phone" id="phone"
                                                    value="{{ old('phone', auth()->user()->phone) }}"
                                                    class="pl-12 form-input"
                                                    placeholder="812 3456 7890">
                                            </div>
                                            @error('phone')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                {{-- Address Section --}}
                                <div class="pt-6 border-t border-gray-200">
                                    <h2 class="text-lg font-semibold text-gray-900">Address</h2>
                                    <p class="mt-1 text-sm text-gray-500">Your shipping and billing address.</p>

                                    <div class="mt-6">
                                        <label for="address" class="block text-sm font-medium text-gray-700">Street Address</label>
                                        <textarea name="address" id="address" rows="3"
                                            class="mt-1 form-input"
                                            placeholder="Enter your complete address">{{ old('address', auth()->user()->address) }}</textarea>
                                        @error('address')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Notification Settings --}}
                                <div class="pt-6 border-t border-gray-200">
                                    <h2 class="text-lg font-semibold text-gray-900">Notification Settings</h2>
                                    <p class="mt-1 text-sm text-gray-500">Manage how you receive notifications.</p>

                                    <div class="mt-6 space-y-4">
                                        <div class="flex items-start">
                                            <div class="flex items-center h-5">
                                                <input type="checkbox" id="email_notifications" name="email_notifications"
                                                    class="w-4 h-4 border-gray-300 rounded text-primary-600 focus:ring-primary-500">
                                            </div>
                                            <div class="ml-3">
                                                <label for="email_notifications" class="font-medium text-gray-700">Email Notifications</label>
                                                <p class="text-sm text-gray-500">Receive notifications about your bookings and reviews via email.</p>
                                            </div>
                                        </div>

                                        <div class="flex items-start">
                                            <div class="flex items-center h-5">
                                                <input type="checkbox" id="sms_notifications" name="sms_notifications"
                                                    class="w-4 h-4 border-gray-300 rounded text-primary-600 focus:ring-primary-500">
                                            </div>
                                            <div class="ml-3">
                                                <label for="sms_notifications" class="font-medium text-gray-700">SMS Notifications</label>
                                                <p class="text-sm text-gray-500">Get important updates about your bookings via SMS.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Form Actions --}}
                            <div class="flex items-center justify-end gap-4 mt-8">
                                <button type="button"
                                        onclick="window.location.href='{{ route('customer.dashboard') }}'"
                                        class="px-4 py-2 text-sm font-medium text-gray-700 transition-colors bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                    Cancel
                                </button>
                                <button type="submit"
                                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-white transition-colors border border-transparent rounded-lg bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                    <svg class="w-4 h-4 mr-2 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Delete Account --}}
                <div class="p-8 mt-6 bg-white shadow-sm rounded-xl">
                    <div class="flex items-start">
                        <div class="p-2 bg-red-100 rounded-lg">
                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h2 class="text-lg font-semibold text-gray-900">Delete Account</h2>
                            <p class="mt-1 text-sm text-gray-500">Once you delete your account, there is no going back. Please be certain.</p>
                            <button type="button" class="inline-flex items-center px-4 py-2 mt-4 text-sm font-medium text-red-600 transition-colors bg-white border border-red-600 rounded-lg hover:bg-red-50">
                                Delete Account
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div x-data="{ show: true }"
             x-show="show"
             x-init="setTimeout(() => show = false, 3000)"
             class="fixed bottom-4 right-4"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform translate-y-2"
             x-transition:enter-end="opacity-100 transform translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform translate-y-0"
             x-transition:leave-end="opacity-0 transform translate-y-2">
            <div class="flex items-center p-4 space-x-4 text-white bg-green-500 rounded-lg shadow-lg">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <p>{{ session('success') }}</p>
            </div>
        </div>
    @endif
</x-app-layout>
