<x-app-layout>
    <div class="py-12 mt-5">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm rounded-xl">
                <div class="p-6">
                    <h2 class="text-lg font-medium text-gray-900">My Bookings</h2>

                    <div class="mt-6">
                        @forelse($bookings as $booking)
                            <div class="p-6 mb-4 bg-white border rounded-lg shadow-sm">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-gray-500">Booking #{{ $booking->id }}</p>
                                        <h3 class="mt-1 text-lg font-semibold">{{ $booking->pickup_location }} → {{ $booking->destination }}</h3>
                                        <div class="mt-2 space-y-1">
                                            <p class="text-sm text-gray-600">{{ $booking->booking_date->format('d M Y H:i') }}</p>
                                            <p class="text-sm text-gray-600">{{ $booking->bus->name }} ({{ $booking->bus->number_plate }})</p>
                                            <p class="font-medium text-gray-900">Rp {{ number_format($booking->total_amount) }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <span @class([
                                            'px-3 py-1 text-xs font-medium rounded-full',
                                            'bg-green-100 text-green-800' => $booking->status === 'completed',
                                            'bg-yellow-100 text-yellow-800' => $booking->status === 'pending',
                                            'bg-blue-100 text-blue-800' => $booking->status === 'confirmed',
                                            'bg-red-100 text-red-800' => $booking->status === 'cancelled',
                                        ])>
                                            {{ ucfirst($booking->status) }}
                                        </span>

                                        <div class="mt-4">
                                            <a href="{{ route('customer.bookings.show', $booking) }}"
                                               class="text-sm font-medium text-primary-600 hover:text-primary-700">
                                                View Details →
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="p-12 text-center">
                                <p class="text-gray-500">You don't have any bookings yet.</p>
                                <a href="{{ route('buses.index') }}" class="inline-block mt-4 btn-primary">
                                    Browse Available Buses
                                </a>
                            </div>
                        @endforelse

                        <div class="mt-6">
                            {{ $bookings->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
