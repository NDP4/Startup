<x-app-layout>
    <div class="py-12 mt-5">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            {{-- Filter Section --}}
            <div class="p-6 mb-6 bg-white rounded-lg shadow-sm">
                <form action="{{ route('buses.index') }}" method="GET" class="grid grid-cols-1 gap-4 md:grid-cols-3">
                    {{-- Search Input --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Cari Bus</label>
                        <input type="text" name="search" value="{{ request('search') }}"
                               class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500">
                    </div>

                    {{-- Date Filter --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Tanggal Sewa</label>
                        <input type="date" name="booking_date" value="{{ request('booking_date') }}"
                               class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500">
                    </div>

                    {{-- More filters... --}}

                    <div class="flex justify-end space-x-2 md:col-span-3">
                        <button type="submit" class="btn-primary">Filter</button>
                        <a href="{{ route('buses.index') }}" class="btn-secondary">Reset</a>
                    </div>
                </form>
            </div>

            {{-- Bus Grid --}}
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                @foreach($buses as $bus)
                    <div class="relative overflow-hidden transition-shadow bg-white shadow-sm rounded-xl hover:shadow-md">
                        {{-- Status Badge --}}
                        <div class="absolute top-2 right-2">
                            <span @class([
                                'px-2 py-1 text-xs font-medium rounded-full',
                                'bg-green-100 text-green-800' => $bus->is_available,
                                'bg-red-100 text-red-800' => !$bus->is_available,
                            ])>
                                {{ $bus->is_available ? 'Available' : 'Booked' }}
                            </span>
                        </div>

                        {{-- Bus Image --}}
                        <div class="aspect-w-16 aspect-h-9">
                            @if($bus->images && count($bus->images) > 0)
                                <img src="{{ Storage::url($bus->images[0]) }}"
                                     alt="{{ $bus->name }}"
                                     class="object-cover w-full h-full">
                            @endif
                        </div>

                        {{-- Bus Info --}}
                        <div class="p-4">
                            <h3 class="text-lg font-semibold">{{ $bus->name }}</h3>
                            <p class="text-sm text-gray-600">{{ $bus->number_plate }}</p>

                            <div class="mt-2 space-y-2">
                                {{-- Capacity --}}
                                <div class="flex items-center text-sm">
                                    <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                    {{ $bus->default_seat_capacity }} Kursi
                                </div>

                                {{-- Price --}}
                                <div class="flex items-center text-sm">
                                    <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    @if($bus->pricing_type === 'daily')
                                        Rp {{ number_format($bus->price_per_day) }}/hari
                                    @else
                                        Rp {{ number_format($bus->price_per_km) }}/km
                                    @endif
                                </div>
                            </div>

                            {{-- Action Buttons --}}
                            <div class="flex mt-4 space-x-2">
                                <a href="{{ route('buses.show', $bus) }}"
                                   class="flex-1 text-center btn-primary">
                                    Detail
                                </a>
                                @auth
                                    @if($bus->is_available)
                                        <a href="{{ route('booking.create', $bus) }}"
                                           class="flex-1 text-center btn-secondary">
                                            Book Now
                                        </a>
                                    @else
                                        <button type="button"
                                                class="flex-1 text-center cursor-not-allowed btn-disabled"
                                                disabled>
                                            Not Available
                                        </button>
                                    @endif
                                @else
                                    <a href="{{ route('filament.panel.auth.login') }}"
                                       class="flex-1 text-center btn-secondary">
                                        Login to Book
                                    </a>
                                @endauth
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="mt-6">
                {{ $buses->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
