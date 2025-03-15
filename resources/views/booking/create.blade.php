<x-app-layout>
    <div class="py-12 mt-5">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm rounded-xl">
                <form action="{{ route('booking.store', $bus) }}" method="POST" class="p-6">
                    @csrf

                    @if(session('error'))
                        <div class="p-4 mb-6 text-red-700 bg-red-100 rounded-lg">
                            {{ session('error') }}
                        </div>
                    @endif

                    {{-- Show validation errors if any --}}
                    @if ($errors->any())
                        <div class="p-4 mb-6 text-red-700 bg-red-100 rounded-lg">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="space-y-8">
                        {{-- Bus Info --}}
                        <div>
                            <h2 class="text-lg font-medium text-gray-900">Bus Information</h2>
                            <div class="flex items-center gap-4 mt-4">
                                @if($bus->images && count($bus->images) > 0)
                                    <img src="{{ Storage::url($bus->images[0]) }}"
                                         alt="{{ $bus->name }}"
                                         class="object-cover w-24 h-24 rounded-lg">
                                @endif
                                <div>
                                    <h3 class="font-medium">{{ $bus->name }}</h3>
                                    <p class="text-sm text-gray-600">{{ $bus->number_plate }}</p>
                                    <p class="mt-1 text-sm text-primary-600">
                                        @if($bus->pricing_type === 'daily')
                                            Rp {{ number_format($bus->price_per_day) }}/day
                                        @else
                                            Rp {{ number_format($bus->price_per_km) }}/km
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- Trip Details --}}
                        <div>
                            <h2 class="text-lg font-medium text-gray-900">Trip Details</h2>
                            <div class="grid grid-cols-1 gap-6 mt-4 sm:grid-cols-2">
                                <div>
                                    <label for="booking_date" class="block text-sm font-medium text-gray-700">Departure Date</label>
                                    <input type="datetime-local" name="booking_date" id="booking_date"
                                           class="mt-1 form-input" required>
                                </div>

                                <div>
                                    <label for="return_date" class="block text-sm font-medium text-gray-700">Return Date</label>
                                    <input type="datetime-local" name="return_date" id="return_date"
                                           class="mt-1 form-input" required>
                                </div>

                                <div class="sm:col-span-2">
                                    <label for="pickup_location" class="block text-sm font-medium text-gray-700">Pickup Location</label>
                                    <input type="text" name="pickup_location" id="pickup_location"
                                           class="mt-1 form-input" required>
                                </div>

                                <div class="sm:col-span-2">
                                    <label for="destination" class="block text-sm font-medium text-gray-700">Destination</label>
                                    <input type="text" name="destination" id="destination"
                                           class="mt-1 form-input" required>
                                </div>
                            </div>
                        </div>

                        {{-- Seat Selection --}}
                        <div>
                            <h2 class="text-lg font-medium text-gray-900">Seat Selection</h2>
                            <div class="grid grid-cols-1 gap-6 mt-4 sm:grid-cols-2">
                                <div>
                                    <label for="total_seats" class="block text-sm font-medium text-gray-700">Number of Seats</label>
                                    <input type="number" name="total_seats" id="total_seats"
                                           class="mt-1 form-input" min="1" max="{{ $bus->default_seat_capacity }}" required>
                                </div>

                                <div>
                                    <label for="seat_type" class="block text-sm font-medium text-gray-700">Seat Type</label>
                                    <select name="seat_type" id="seat_type" class="mt-1 form-input" required>
                                        <option value="standard">Standard</option>
                                        <option value="legrest">Legrest (+{{ number_format($bus->legrest_price_per_seat) }})</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- Additional Requests --}}
                        <div>
                            <h2 class="text-lg font-medium text-gray-900">Additional Requests</h2>
                            <div class="mt-4">
                                <textarea name="special_requests" id="special_requests" rows="4"
                                          class="form-input" placeholder="Any special requests?"></textarea>
                            </div>
                        </div>

                        {{-- Submit Button --}}
                        <div class="flex justify-end gap-4">
                            <a href="{{ route('buses.index') }}" class="btn-secondary">Cancel</a>
                            <button type="submit" class="btn-primary">
                                Continue to Payment
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
