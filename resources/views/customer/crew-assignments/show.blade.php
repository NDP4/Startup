<x-app-layout>
    <div class="py-12 mt-5">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm rounded-xl">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-lg font-medium text-gray-900">Crew Assignment Details</h2>
                        <a href="{{ route('customer.crew-assignments.index') }}"
                           class="text-sm font-medium text-primary-600 hover:text-primary-700">
                            ← Back to List
                        </a>
                    </div>

                    {{-- Crew Info --}}
                    <div class="p-6 mb-6 rounded-lg bg-gray-50">
                        <div class="flex items-center gap-4">
                            <div class="flex items-center justify-center w-16 h-16 rounded-full bg-primary-100">
                                <span class="text-2xl font-medium text-primary-600">
                                    {{ strtoupper(substr($assignment->crew->name, 0, 2)) }}
                                </span>
                            </div>
                            <div>
                                <h3 class="text-xl font-medium text-gray-900">{{ $assignment->crew->name }}</h3>
                                <p class="text-gray-500">{{ $assignment->role }}</p>
                                @if($assignment->crew->phone)
                                    <p class="mt-2 text-sm text-gray-600">
                                        <span class="font-medium">Contact:</span> {{ $assignment->crew->phone }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Trip Details --}}
                    <div class="grid gap-6 md:grid-cols-2">
                        <div>
                            <h4 class="mb-2 text-sm font-medium text-gray-700">Trip Details</h4>
                            <div class="p-4 space-y-2 rounded-lg bg-gray-50">
                                <p><span class="font-medium">Booking ID:</span> #{{ $assignment->booking->id }}</p>
                                <p><span class="font-medium">From:</span> {{ $assignment->booking->pickup_location }}</p>
                                <p><span class="font-medium">To:</span> {{ $assignment->booking->destination }}</p>
                                <p><span class="font-medium">Date:</span> {{ $assignment->booking->booking_date->format('d M Y H:i') }}</p>
                                @if($assignment->booking->return_date)
                                    <p><span class="font-medium">Return:</span> {{ $assignment->booking->return_date->format('d M Y H:i') }}</p>
                                @endif
                            </div>
                        </div>

                        <div>
                            <h4 class="mb-2 text-sm font-medium text-gray-700">Bus Information</h4>
                            <div class="p-4 space-y-2 rounded-lg bg-gray-50">
                                <p><span class="font-medium">Bus:</span> {{ $assignment->booking->bus->name }}</p>
                                <p><span class="font-medium">Plate Number:</span> {{ $assignment->booking->bus->number_plate }}</p>
                                <p><span class="font-medium">Seats:</span> {{ $assignment->booking->total_seats }} ({{ ucfirst($assignment->booking->seat_type) }})</p>
                            </div>
                        </div>
                    </div>

                    {{-- Notes & Instructions --}}
                    @if($assignment->notes)
                        <div class="mt-6">
                            <h4 class="mb-2 text-sm font-medium text-gray-700">Notes & Instructions</h4>
                            <div class="p-4 rounded-lg bg-gray-50">
                                <p class="text-gray-600">{{ $assignment->notes }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
