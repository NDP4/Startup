<x-app-layout>
    <div class="py-12 mt-5">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm rounded-xl">
                <div class="p-6">
                    <h2 class="text-lg font-medium text-gray-900">My Reviews</h2>

                    <div class="mt-6">
                        @forelse($reviews as $review)
                            <div class="p-6 mb-4 bg-white border rounded-lg shadow-sm">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <h3 class="font-medium">{{ $review->booking->bus->name }}</h3>
                                            <span class="text-sm text-gray-500">
                                                {{ $review->created_at->format('d M Y') }}
                                            </span>
                                        </div>

                                        {{-- Rating --}}
                                        <div class="flex mt-2 text-yellow-400">
                                            @for($i = 1; $i <= 5; $i++)
                                                <svg class="w-5 h-5 {{ $i <= $review->bus_rating ? 'text-yellow-400' : 'text-gray-300' }}"
                                                     fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                </svg>
                                            @endfor
                                        </div>

                                        {{-- Comment --}}
                                        <p class="mt-2 text-gray-600">{{ $review->bus_comment }}</p>

                                        {{-- Trip Info --}}
                                        <div class="mt-4 text-sm text-gray-500">
                                            {{ $review->booking->pickup_location }} → {{ $review->booking->destination }}
                                            <span class="mx-2">•</span>
                                            {{ $review->booking->booking_date->format('d M Y') }}
                                        </div>
                                    </div>

                                    {{-- Actions --}}
                                    <div>
                                        <a href="{{ route('customer.bookings.show', $review->booking) }}"
                                           class="text-sm font-medium text-primary-600 hover:text-primary-700">
                                            View Booking →
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="p-12 text-center">
                                <p class="text-gray-500">You haven't written any reviews yet.</p>
                                <a href="{{ route('customer.bookings.index') }}" class="inline-block mt-4 btn-primary">
                                    View Your Bookings
                                </a>
                            </div>
                        @endforelse

                        <div class="mt-6">
                            {{ $reviews->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
