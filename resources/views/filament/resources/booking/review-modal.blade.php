<div>
    <div class="p-4 mb-4 text-sm border rounded-lg shadow-sm bg-gray-50">
        <div class="flex items-center justify-between mb-2">
            <div>
                <p class="text-xs font-medium text-gray-500">Booking #{{ $booking->id }}</p>
                <h3 class="text-lg font-semibold text-gray-900">{{ $booking->pickup_location }} → {{ $booking->destination }}</h3>
            </div>
            <span @class([
                'px-3 py-1 text-xs font-medium rounded-full',
                'bg-warning-100 text-warning-700' => $booking->status === 'pending',
                'bg-success-100 text-success-700' => $booking->status === 'completed',
            ])>
                {{ ucfirst($booking->status) }}
            </span>
        </div>
        <p class="text-sm text-gray-600">{{ $booking->booking_date->format('d F Y H:i') }}</p>
    </div>

    {{-- Form content will be automatically inserted here by Filament --}}
</div>
