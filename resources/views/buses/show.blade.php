<x-app-layout>
    <div class="py-12 mt-10">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm rounded-xl">
                <div class="p-6">
                    <div class="grid grid-cols-1 gap-8 md:grid-cols-2">
                        {{-- Image Gallery --}}
                        <div class="space-y-4">
                            <div class="aspect-w-16 aspect-h-9">
                                <img id="mainImage"
                                     src="{{ $bus->main_image ? Storage::url($bus->main_image) : asset('images/bus-placeholder.jpg') }}"
                                     alt="{{ $bus->name }}"
                                     class="object-cover w-full h-full rounded-lg">
                            </div>
                            @if(count($bus->all_images) > 0)
                                <div class="grid grid-cols-4 gap-2">
                                    @foreach($bus->all_images as $index => $image)
                                        <button onclick="document.getElementById('mainImage').src = '{{ Storage::url($image['url']) }}'"
                                                class="relative aspect-square group">
                                            <img src="{{ Storage::url($image['url']) }}"
                                                 alt="{{ $image['description'] ?? $bus->name . ' image ' . ($index + 1) }}"
                                                 class="object-cover w-full h-full transition duration-300 rounded-lg hover:opacity-75">
                                            @if($image['description'])
                                                <div class="absolute inset-0 flex items-end justify-center p-2 transition duration-300 bg-black/0 group-hover:bg-black/40">
                                                    <p class="text-xs text-white opacity-0 group-hover:opacity-100">
                                                        {{ $image['description'] }}
                                                    </p>
                                                </div>
                                            @endif
                                        </button>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        {{-- Bus Info --}}
                        <div class="space-y-6">
                            <div>
                                <h1 class="text-2xl font-bold">{{ $bus->name }}</h1>
                                <p class="text-gray-600">{{ $bus->number_plate }}</p>
                            </div>

                            {{-- Rating --}}
                            <div class="flex items-center">
                                <div class="flex text-yellow-400">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg class="w-5 h-5 {{ $i <= ceil($averageRating) ? 'text-yellow-400' : 'text-gray-300' }}"
                                             fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    @endfor
                                </div>
                                <p class="ml-2 text-sm text-gray-600">
                                    {{ number_format($averageRating, 1) }} ({{ $totalReviews }} ulasan)
                                </p>
                            </div>

                            {{-- Price --}}
                            <div class="text-2xl font-bold text-primary-600">
                                @if($bus->pricing_type === 'daily')
                                    Rp {{ number_format($bus->price_per_day) }}/hari
                                @else
                                    Rp {{ number_format($bus->price_per_km) }}/km
                                @endif
                            </div>

                            {{-- Features --}}
                            <div class="grid grid-cols-2 gap-4">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                    {{ $bus->default_seat_capacity }} Kursi
                                </div>
                                {{-- Add more features here --}}
                            </div>

                            {{-- Description --}}
                            <div>
                                <h3 class="mb-2 font-semibold">Deskripsi</h3>
                                <p class="text-gray-600">{{ $bus->description }}</p>
                            </div>

                            {{-- Action Button --}}
                            <div class="pt-6">
                                @auth
                                    <a href="{{ route('booking.create', $bus) }}"
                                       class="w-full text-center btn-primary">
                                        Booking Sekarang
                                    </a>
                                @else
                                    <a href="{{ route('login') }}"
                                       class="w-full text-center btn-secondary">
                                        Login untuk Booking
                                    </a>
                                @endauth
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Reviews Section --}}
            <div class="mt-8 overflow-hidden bg-white shadow-sm rounded-xl">
                <div class="p-6">
                    <h2 class="mb-4 text-xl font-semibold">Ulasan Pelanggan</h2>
                    @foreach($bus->reviews as $review)
                        <div class="py-4 border-b border-gray-200 last:border-0">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="font-medium">{{ $review->customer->name }}</p>
                                    <div class="flex mt-1 text-yellow-400">
                                        @for($i = 1; $i <= 5; $i++)
                                            <svg class="w-4 h-4 {{ $i <= $review->bus_rating ? 'text-yellow-400' : 'text-gray-300' }}"
                                                 fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                        @endfor
                                    </div>
                                </div>
                                <span class="text-sm text-gray-500">
                                    {{ $review->created_at->format('d M Y') }}
                                </span>
                            </div>
                            <p class="mt-2 text-gray-600">{{ $review->bus_comment }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function changeMainImage(url) {
            document.getElementById('mainImage').src = url;
        }
    </script>
    @endpush
</x-app-layout>
