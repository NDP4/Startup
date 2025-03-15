<x-app-layout>
    <div class="py-12 mt-5">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm rounded-xl">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-lg font-medium text-gray-900">Booking Details</h2>
                        <span @class([
                            'px-3 py-1 text-sm font-medium rounded-full',
                            'bg-green-100 text-green-800' => $booking->status === 'completed',
                            'bg-yellow-100 text-yellow-800' => $booking->status === 'pending',
                            'bg-blue-100 text-blue-800' => $booking->status === 'confirmed',
                            'bg-red-100 text-red-800' => $booking->status === 'cancelled',
                        ])>
                            {{ ucfirst($booking->status) }}
                        </span>
                    </div>

                    {{-- Bus Details --}}
                    <div class="p-4 mb-6 rounded-lg bg-gray-50">
                        <div class="flex gap-4">
                            @if($booking->bus->images && count($booking->bus->images) > 0)
                                <img src="{{ Storage::url($booking->bus->images[0]) }}"
                                     class="object-cover w-24 h-24 rounded-lg">
                            @endif
                            <div>
                                <h3 class="font-medium">{{ $booking->bus->name }}</h3>
                                <p class="text-sm text-gray-600">{{ $booking->bus->number_plate }}</p>
                                <p class="mt-2 text-sm">{{ $booking->total_seats }} Seats ({{ ucfirst($booking->seat_type) }})</p>
                            </div>
                        </div>
                    </div>

                    {{-- Trip Details --}}
                    <div class="grid gap-6 mb-6 md:grid-cols-2">
                        <div>
                            <h4 class="mb-2 text-sm font-medium text-gray-700">Trip Details</h4>
                            <div class="space-y-2">
                                <p><span class="text-gray-600">From:</span> {{ $booking->pickup_location }}</p>
                                <p><span class="text-gray-600">To:</span> {{ $booking->destination }}</p>
                                <p><span class="text-gray-600">Date:</span> {{ $booking->booking_date->format('d M Y H:i') }}</p>
                                @if($booking->return_date)
                                    <p><span class="text-gray-600">Return:</span> {{ $booking->return_date->format('d M Y H:i') }}</p>
                                @endif
                            </div>
                        </div>

                        <div>
                            <h4 class="mb-2 text-sm font-medium text-gray-700">Payment Details</h4>
                            <div class="space-y-2">
                                <p><span class="text-gray-600">Total Amount:</span> Rp {{ number_format($booking->total_amount) }}</p>
                                <p><span class="text-gray-600">Payment Status:</span>
                                    <span @class([
                                        'px-2 py-0.5 text-xs font-medium rounded-full',
                                        'bg-green-100 text-green-800' => $booking->payment_status === 'paid',
                                        'bg-yellow-100 text-yellow-800' => $booking->payment_status === 'pending',
                                        'bg-red-100 text-red-800' => $booking->payment_status === 'failed',
                                    ])>
                                        {{ ucfirst($booking->payment_status) }}
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="flex justify-end gap-4">
                        <a href="{{ route('customer.bookings.index') }}" class="btn-secondary">
                            Back to Bookings
                        </a>
                        @if($booking->canRetryPayment())
                            <a href="{{ route('payment.checkout', $booking) }}" class="btn-primary">
                                Pay Now
                            </a>
                        @else
                            @if($booking->payment_status === 'paid')
                                <a href="{{ route('booking.receipt', $booking) }}" class="btn-primary">
                                    <svg class="inline-block w-5 h-5 mr-2 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    View Receipt
                                </a>
                            @endif
                        @endif
                        @if($booking->status === 'completed' && !$booking->review)
                            <a href="{{ route('reviews.create', $booking) }}" class="btn-primary">
                                Write Review
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
