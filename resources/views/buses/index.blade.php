<x-app-layout>
    <div class="py-8 mt-10 bg-gray-50">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            {{-- Filter Section --}}
            <div class="p-6 mb-8 bg-white shadow-sm rounded-xl">
                <h2 class="mb-6 text-xl font-semibold text-gray-800">Filter Pencarian</h2>
                <form action="{{ route('buses.index') }}" method="GET" class="grid grid-cols-1 gap-6 md:grid-cols-3">
                    {{-- Search Input --}}
                    <div class="space-y-2">
                        <label class="text-sm font-medium text-gray-700">Cari Bus</label>
                        <div class="relative">
                            <input type="text" name="search" value="{{ request('search') }}"
                                   class="block w-full pl-10 form-input"
                                   placeholder="Cari berdasarkan nama atau nomor plat">
                        </div>
                    </div>

                    {{-- Date Filter --}}
                    <div class="space-y-2">
                        <label class="text-sm font-medium text-gray-700">Tanggal Sewa</label>
                        <div class="relative">
                            <input type="date" name="booking_date" value="{{ request('booking_date') }}"
                                   class="block w-full pl-10 form-input">
                        </div>
                    </div>

                    <div class="flex items-end justify-end gap-3 md:col-span-3">
                        <a href="{{ route('buses.index') }}"
                           class="px-6 btn-secondary">
                            Reset
                        </a>
                        <button type="submit" class="px-6 btn-primary">
                            Terapkan Filter
                        </button>
                    </div>
                </form>
            </div>

            {{-- Bus Grid --}}
            <div class="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3">
                @foreach($buses as $bus)
                    <div class="overflow-hidden transition-all duration-300 bg-white border border-gray-100 rounded-xl hover:shadow-lg hover:-translate-y-1">
                        {{-- Bus Image --}}
                        <div x-data="{ currentImage: 0, images: {{ json_encode($bus->all_images) }} }"
                             class="relative aspect-w-16 aspect-h-10">
                            <template x-if="images.length > 0">
                                <div class="relative w-full h-full">
                                    <img :src="'/storage/' + images[currentImage].url"
                                         :alt="images[currentImage].description || '{{ $bus->name }}'"
                                         class="object-cover w-full h-full">

                                    {{-- Image Navigation --}}
                                    <div class="absolute inset-0 flex items-center justify-between p-2">
                                        <button x-show="images.length > 1"
                                                @click="currentImage = (currentImage - 1 + images.length) % images.length"
                                                class="p-1 text-white rounded-full bg-black/50 hover:bg-black/70">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                            </svg>
                                        </button>
                                        <button x-show="images.length > 1"
                                                @click="currentImage = (currentImage + 1) % images.length"
                                                class="p-1 text-white rounded-full bg-black/50 hover:bg-black/70">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                            </svg>
                                        </button>
                                    </div>

                                    {{-- Image Indicators --}}
                                    <div x-show="images.length > 1"
                                         class="absolute bottom-0 flex justify-center w-full gap-1 p-2">
                                        <template x-for="(image, index) in images" :key="index">
                                            <button @click="currentImage = index"
                                                    :class="{'bg-white': currentImage === index, 'bg-white/50': currentImage !== index}"
                                                    class="w-2 h-2 transition-all duration-300 rounded-full">
                                            </button>
                                        </template>
                                    </div>
                                </div>
                            </template>
                            <template x-if="images.length === 0">
                                <img src="{{ asset('images/bus-placeholder.jpg') }}"
                                     alt="{{ $bus->name }}"
                                     class="object-cover w-full h-full">
                            </template>

                            {{-- Status Badge --}}
                            <div class="absolute top-3 right-3">
                                <span @class([
                                    'px-3 py-1 text-sm font-medium rounded-full shadow-sm',
                                    'bg-green-100 text-green-700 border border-green-200' => $bus->is_available,
                                    'bg-red-100 text-red-700 border border-red-200' => !$bus->is_available,
                                ])>
                                    {{ $bus->is_available ? 'Available' : 'Booked' }}
                                </span>
                            </div>
                        </div>

                        {{-- Bus Info --}}
                        <div class="p-6">
                            <div class="mb-4">
                                <h3 class="text-lg font-semibold text-gray-900">{{ $bus->name }}</h3>
                                <p class="mt-1 text-sm text-gray-600">{{ $bus->number_plate }}</p>
                            </div>

                            <div class="grid grid-cols-2 gap-4 py-4 border-t border-gray-100">
                                {{-- Capacity --}}
                                <div class="flex items-center text-sm text-gray-700">
                                    <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                    <span>{{ $bus->default_seat_capacity }} Kursi</span>
                                </div>

                                {{-- Price --}}
                                <div class="flex items-center text-sm font-medium text-gray-900">
                                    <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                            <div class="grid grid-cols-2 gap-3 pt-4 border-t border-gray-100">
                                <a href="{{ route('buses.show', $bus) }}"
                                   class="btn-secondary">
                                    Lihat Detail
                                </a>
                                @auth
                                    @if($bus->is_available)
                                        <a href="{{ route('booking.create', $bus) }}"
                                           class="btn-primary">
                                            Pesan Sekarang
                                        </a>
                                    @else
                                        <button type="button"
                                                class="btn-disabled"
                                                disabled>
                                            Tidak Tersedia
                                        </button>
                                    @endif
                                @else
                                    <a href="{{ route('filament.panel.auth.login') }}"
                                       class="btn-primary">
                                        Login untuk Pesan
                                    </a>
                                @endauth
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="mt-8">
                {{ $buses->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
