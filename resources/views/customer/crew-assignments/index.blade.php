<x-app-layout>
    <div class="py-12 mt-5">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm rounded-xl">
                <div class="p-6">
                    <h2 class="text-lg font-medium text-gray-900">Assigned Crew</h2>

                    <div class="mt-6">
                        @forelse($assignments as $assignment)
                            <div class="p-6 mb-4 bg-white border rounded-lg shadow-sm">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <div class="flex items-center gap-4">
                                            <div class="flex items-center justify-center w-12 h-12 rounded-full bg-primary-100">
                                                <span class="text-lg font-medium text-primary-600">
                                                    {{ strtoupper(substr($assignment->crew->name, 0, 2)) }}
                                                </span>
                                            </div>
                                            <div>
                                                <h3 class="font-medium text-gray-900">{{ $assignment->crew->name }}</h3>
                                                <p class="text-sm text-gray-500">{{ $assignment->role }}</p>
                                            </div>
                                        </div>

                                        <div class="mt-4 space-y-1 text-sm">
                                            <p class="text-gray-600">
                                                <span class="font-medium">Booking:</span>
                                                #{{ $assignment->booking->id }}
                                            </p>
                                            <p class="text-gray-600">
                                                <span class="font-medium">Trip:</span>
                                                {{ $assignment->booking->pickup_location }} → {{ $assignment->booking->destination }}
                                            </p>
                                            <p class="text-gray-600">
                                                <span class="font-medium">Date:</span>
                                                {{ $assignment->booking->booking_date->format('d M Y H:i') }}
                                            </p>
                                        </div>
                                    </div>

                                    <div class="text-right">
                                        <a href="{{ route('customer.crew-assignments.show', $assignment) }}"
                                           class="text-sm font-medium text-primary-600 hover:text-primary-700">
                                            View Details →
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="p-12 text-center">
                                <p class="text-gray-500">No crew assignments found.</p>
                            </div>
                        @endforelse

                        <div class="mt-6">
                            {{ $assignments->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
