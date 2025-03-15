<x-app-layout>
    <div class="py-12 mt-5">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            {{-- Stats Overview --}}
            <div class="grid grid-cols-1 gap-6 mb-6 md:grid-cols-3">
                <div class="overflow-hidden bg-white shadow-sm rounded-xl">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 p-3 rounded-full bg-primary-100">
                                <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h2 class="text-sm font-medium text-gray-900">Total Bookings</h2>
                                <p class="text-2xl font-semibold text-gray-700">{{ auth()->user()->bookings()->count() }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="overflow-hidden bg-white shadow-sm rounded-xl">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 p-3 bg-green-100 rounded-full">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h2 class="text-sm font-medium text-gray-900">Completed Trips</h2>
                                <p class="text-2xl font-semibold text-gray-700">
                                    {{ auth()->user()->bookings()->where('status', 'completed')->count() }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="overflow-hidden bg-white shadow-sm rounded-xl">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 p-3 bg-yellow-100 rounded-full">
                                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h2 class="text-sm font-medium text-gray-900">Reviews Given</h2>
                                <p class="text-2xl font-semibold text-gray-700">
                                    {{ auth()->user()->reviews()->count() }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Recent Bookings --}}
            <div class="overflow-hidden bg-white shadow-sm rounded-xl">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900">Recent Bookings</h3>
                    <div class="flow-root mt-6">
                        <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                            <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                                <table class="min-w-full divide-y divide-gray-300">
                                    <thead>
                                        <tr>
                                            <th class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900">Bus</th>
                                            <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Date</th>
                                            <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Status</th>
                                            <th class="px-3 py-3.5 text-right text-sm font-semibold text-gray-900">Amount</th>
                                            <th class="relative py-3.5 pl-3 pr-4 sm:pr-6 lg:pr-8">
                                                <span class="sr-only">Actions</span>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        @forelse(auth()->user()->bookings()->latest()->take(5)->get() as $booking)
                                            <tr>
                                                <td class="py-4 pl-4 pr-3 text-sm whitespace-nowrap">
                                                    <div class="font-medium text-gray-900">{{ $booking->bus->name }}</div>
                                                    <div class="text-gray-500">{{ $booking->bus->number_plate }}</div>
                                                </td>
                                                <td class="px-3 py-4 text-sm text-gray-500 whitespace-nowrap">
                                                    {{ $booking->booking_date->format('d M Y H:i') }}
                                                </td>
                                                <td class="px-3 py-4 text-sm whitespace-nowrap">
                                                    <span @class([
                                                        'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium',
                                                        'bg-green-100 text-green-800' => $booking->status === 'completed',
                                                        'bg-yellow-100 text-yellow-800' => $booking->status === 'pending',
                                                        'bg-blue-100 text-blue-800' => $booking->status === 'confirmed',
                                                        'bg-red-100 text-red-800' => $booking->status === 'cancelled',
                                                    ])>
                                                        {{ ucfirst($booking->status) }}
                                                    </span>
                                                </td>
                                                <td class="px-3 py-4 text-sm text-right text-gray-500 whitespace-nowrap">
                                                    Rp {{ number_format($booking->total_amount) }}
                                                </td>
                                                <td class="relative py-4 pl-3 pr-4 text-sm font-medium text-right whitespace-nowrap sm:pr-6 lg:pr-8">
                                                    <a href="{{ route('customer.bookings.show', $booking) }}" class="text-primary-600 hover:text-primary-900">
                                                        View<span class="sr-only">, booking {{ $booking->id }}</span>
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="px-3 py-4 text-sm text-center text-gray-500">
                                                    No bookings found
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
