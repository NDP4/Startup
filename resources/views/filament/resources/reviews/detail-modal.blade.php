<div class="p-4 space-y-4">
    {{-- Header Info --}}
    <div class="p-4 rounded-lg bg-gray-50">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Booking #{{ $review->booking->id }}</p>
                <h3 class="text-lg font-bold text-gray-900">{{ $review->booking->pickup_location }} → {{ $review->booking->destination }}</h3>
                <p class="text-sm text-gray-600">{{ $review->booking->booking_date->format('d F Y H:i') }}</p>
            </div>
            <div>
                <span @class([
                    'px-3 py-1 text-xs font-medium rounded-full',
                    'bg-success-100 text-success-700' => $review->booking->status === 'completed',
                    'bg-warning-100 text-warning-700' => $review->booking->status === 'pending',
                ])>
                    {{ ucfirst($review->booking->status) }}
                </span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4">
        {{-- Bus Review Section --}}
        <div class="p-4 border rounded-lg">
            <div class="flex items-center gap-3 mb-4">
                <div class="flex items-center justify-center w-10 h-10 rounded-full bg-warning-100">
                    <x-heroicon-s-truck class="w-6 h-6 text-warning-600" />
                </div>
                <div>
                    <h4 class="font-semibold text-gray-900">{{ $review->bus->name }}</h4>
                    <p class="text-sm text-gray-500">Bus Review</p>
                </div>
            </div>

            <div class="space-y-3">
                <div>
                    <p class="mb-1 text-sm font-medium text-gray-600">Rating</p>
                    <div class="flex items-center gap-2">
                        <div class="flex">
                            @for($i = 1; $i <= 5; $i++)
                                <span @class([
                                    'text-lg',
                                    'text-warning-500' => $i <= $review->bus_rating,
                                    'text-gray-300' => $i > $review->bus_rating,
                                ])>★</span>
                            @endfor
                        </div>
                        <span class="text-sm font-medium text-gray-700">
                            {{ $review->bus_rating }}/5
                        </span>
                    </div>
                </div>

                <div>
                    <p class="mb-1 text-sm font-medium text-gray-600">Ulasan</p>
                    <p class="p-3 text-gray-900 rounded-lg bg-gray-50">
                        {{ $review->bus_comment }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Crew Review Section --}}
        @if($review->crew)
        <div class="p-4 border rounded-lg">
            <div class="flex items-center gap-3 mb-4">
                <div class="flex items-center justify-center w-10 h-10 rounded-full bg-primary-100">
                    <x-heroicon-s-user class="w-6 h-6 text-primary-600" />
                </div>
                <div>
                    <h4 class="font-semibold text-gray-900">{{ $review->crew->name }}</h4>
                    <p class="text-sm text-gray-500">Crew Review</p>
                </div>
            </div>

            <div class="space-y-3">
                <div>
                    <p class="mb-1 text-sm font-medium text-gray-600">Rating</p>
                    <div class="flex items-center gap-2">
                        <div class="flex">
                            @for($i = 1; $i <= 5; $i++)
                                <span @class([
                                    'text-lg',
                                    'text-warning-500' => $i <= $review->crew_rating,
                                    'text-gray-300' => $i > $review->crew_rating,
                                ])>★</span>
                            @endfor
                        </div>
                        <span class="text-sm font-medium text-gray-700">
                            {{ $review->crew_rating }}/5
                        </span>
                    </div>
                </div>

                <div>
                    <p class="mb-1 text-sm font-medium text-gray-600">Ulasan</p>
                    <p class="p-3 text-gray-900 rounded-lg bg-gray-50">
                        {{ $review->crew_comment }}
                    </p>
                </div>
            </div>
        </div>
        @endif
    </div>

    {{-- Footer Info --}}
    <div class="flex items-center justify-between pt-4 text-sm text-gray-500 border-t">
        <div>
            Direview oleh: <span class="font-medium text-gray-900">{{ $review->booking->customer->name }}</span>
        </div>
        <div>
            {{ $review->created_at->format('d F Y H:i') }}
        </div>
    </div>
</div>
