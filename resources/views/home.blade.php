<x-app-layout>
    <!-- Hero Section -->
    <div class="pt-32 pb-20 hero-pattern">
        <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="text-center">
                <h1 class="text-4xl font-bold text-white sm:text-5xl md:text-6xl">
                    Perjalanan Nyaman dengan Bus Berkualitas
                </h1>
                <p class="max-w-md mx-auto mt-3 text-base text-blue-100 sm:text-lg md:mt-5 md:text-xl md:max-w-3xl">
                    Nikmati perjalanan dengan armada bus modern kami. Layanan profesional untuk berbagai kebutuhan perjalanan Anda.
                </p>
                <div class="mt-10">
                    @auth
                        <a href="{{ route('filament.panel.resources.bookings.create') }}" class="inline-flex items-center px-6 py-3 text-base font-medium transition-colors bg-white border border-transparent rounded-lg text-primary-600 hover:bg-gray-50">
                            Pesan Sekarang
                            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                            </svg>
                        </a>
                    @else
                        <a href="/panel/register" class="inline-flex items-center px-6 py-3 text-base font-medium transition-colors bg-white border border-transparent rounded-lg text-primary-600 hover:bg-gray-50">
                            Daftar & Pesan
                            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                            </svg>
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </div>

    <!-- Statistik Section -->
    <div class="py-12 bg-white">
        <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
                <div class="p-6 text-center bg-gray-50 rounded-xl">
                    <div class="text-4xl font-bold text-primary-600">{{ \App\Models\Booking::where('status', 'completed')->count() }}+</div>
                    <div class="mt-2 text-sm text-gray-600">Perjalanan Sukses</div>
                </div>
                <div class="p-6 text-center bg-gray-50 rounded-xl">
                    <div class="text-4xl font-bold text-primary-600">{{ \App\Models\User::where('role', 'customer')->count() }}+</div>
                    <div class="mt-2 text-sm text-gray-600">Pelanggan Setia</div>
                </div>
                <div class="p-6 text-center bg-gray-50 rounded-xl">
                    <div class="text-4xl font-bold text-primary-600">{{ \App\Models\Bus::count() }}</div>
                    <div class="mt-2 text-sm text-gray-600">Armada Bus</div>
                </div>
                <div class="p-6 text-center bg-gray-50 rounded-xl">
                    <div class="text-4xl font-bold text-primary-600">4.8</div>
                    <div class="mt-2 text-sm text-gray-600">Rating Pelanggan</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bus Showcase Section -->
    <div class="py-16 bg-gray-50">
        <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="text-center">
                <h2 class="text-3xl font-bold text-gray-900">Armada Bus Kami</h2>
                <p class="mt-4 text-lg text-gray-600">Pilihan bus premium untuk kenyamanan perjalanan Anda</p>
            </div>

            <div class="grid gap-8 mt-12 sm:grid-cols-2 lg:grid-cols-3">
                @foreach(\App\Models\Bus::where('status', 'available')->take(3)->get() as $bus)
                <div class="overflow-hidden bg-white rounded-xl">
                    @if($bus->images && count($bus->images) > 0)
                        <img src="{{ Storage::url($bus->images[0]) }}"
                             alt="{{ $bus->name }}"
                             class="object-cover w-full h-48">
                    @endif
                    <div class="p-6">
                        <h3 class="text-xl font-semibold">{{ $bus->name }}</h3>
                        <div class="flex items-center mt-2">
                            <div class="flex text-amber-400">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg class="w-5 h-5" fill="{{ $i <= 4 ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                    </svg>
                                @endfor
                            </div>
                            <span class="ml-2 text-sm text-gray-600">4.8/5</span>
                        </div>
                        <div class="mt-4 space-y-2 text-sm text-gray-600">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21v-2a4 4 0 00-4-4H9a4 4 0 00-4 4v2"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11a4 4 0 100-8 4 4 0 000 8z"></path>
                                </svg>
                                {{ $bus->default_seat_capacity }} Kursi
                            </div>
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Mulai dari Rp {{ number_format($bus->price_per_day) }}/hari
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Testimonials Section -->
    <div class="py-16 bg-white">
        <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="text-center">
                <h2 class="text-3xl font-bold text-gray-900">Apa Kata Mereka</h2>
                <p class="mt-4 text-lg text-gray-600">Testimoni dari pelanggan yang telah menggunakan layanan kami</p>
            </div>

            <!-- Bus Reviews -->
            <div class="mt-12">
                <h3 class="text-2xl font-semibold text-gray-900">Review Bus</h3>
                @php
                    $reviewQuery = \App\Models\Review::query()
                        ->with(['customer', 'booking', 'bus'])
                        ->whereNotNull('bus_rating')
                        ->where('bus_rating', '>=', 4)
                        ->whereHas('bus')
                        ->latest();

                    // Hapus global scope untuk bagian ini
                    $reviewQuery->withoutGlobalScope('access');

                    $busReviews = $reviewQuery->take(3)->get();
                @endphp

                <div class="grid gap-8 mt-6 sm:grid-cols-2 lg:grid-cols-3">
                    @forelse($busReviews as $review)
                        <div class="relative p-6 bg-gray-50 rounded-xl">
                            <div class="absolute top-6 right-6">
                                @if($review->bus->images && count($review->bus->images) > 0)
                                    <img src="{{ Storage::url($review->bus->images[0]) }}"
                                         alt="{{ $review->bus->name }}"
                                         class="object-cover w-16 h-16 rounded-lg"
                                         onerror="this.src='https://via.placeholder.com/64?text=Bus'">
                                @endif
                            </div>
                            <div class="flex text-amber-400">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg class="w-5 h-5" fill="{{ $i <= $review->bus_rating ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                    </svg>
                                @endfor
                            </div>
                            <div class="mt-4">
                                <p class="font-medium text-primary-600">{{ $review->bus->name }}</p>
                                <p class="mt-1 text-sm text-gray-500">
                                    {{ $review->booking->pickup_location }} → {{ $review->booking->destination }}
                                </p>
                            </div>
                            <p class="mt-3 text-gray-600">{{ Str::limit($review->bus_comment, 120) }}</p>
                            <div class="flex items-center mt-6">
                                <div class="flex-shrink-0">
                                    <svg class="w-10 h-10 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-6-3a2 2 0 11-4 0 2 2 0 014 0zm-2 4a5 5 0 00-4.546 2.916A5.986 5.986 0 0010 16a5.986 5.986 0 004.546-2.084A5 5 0 0010 11z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-900">{{ $review->customer->name }}</p>
                                    <p class="text-sm text-gray-500">{{ $review->created_at->format('d M Y') }}</p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full">
                            <div class="p-6 text-center bg-gray-50 rounded-xl">
                                <p class="text-gray-500">Belum ada ulasan bus</p>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Crew Reviews -->
            @php
                $crewQuery = \App\Models\Review::query()
                    ->with(['customer', 'booking', 'crew'])
                    ->whereNotNull('crew_id')
                    ->whereNotNull('crew_rating')
                    ->where('crew_rating', '>=', 4)
                    ->whereHas('crew')
                    ->latest();

                // Hapus global scope untuk bagian ini
                $crewQuery->withoutGlobalScope('access');

                $crewReviews = $crewQuery->take(3)->get();
            @endphp

            @if($crewReviews->isNotEmpty())
                <div class="mt-16">
                    <h3 class="text-2xl font-semibold text-gray-900">Review Crew</h3>
                    <div class="grid gap-8 mt-6 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach($crewReviews as $review)
                            <div class="p-6 bg-gray-50 rounded-xl">
                                <div class="flex text-amber-400">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg class="w-5 h-5" fill="{{ $i <= $review->crew_rating ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                        </svg>
                                    @endfor
                                </div>
                                <div class="mt-4">
                                    <p class="font-medium text-primary-600">{{ $review->crew->name }}</p>
                                    <p class="mt-1 text-sm text-gray-500">
                                        {{ $review->booking->pickup_location }} → {{ $review->booking->destination }}
                                    </p>
                                </div>
                                <p class="mt-3 text-gray-600">{{ Str::limit($review->crew_comment, 120) }}</p>
                                <div class="flex items-center mt-6">
                                    <div class="flex-shrink-0">
                                        <svg class="w-10 h-10 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-6-3a2 2 0 11-4 0 2 2 0 014 0zm-2 4a5 5 0 00-4.546 2.916A5.986 5.986 0 0010 16a5.986 5.986 0 004.546-2.084A5 5 0 0010 11z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900">{{ $review->customer->name }}</p>
                                        <p class="text-sm text-gray-500">{{ $review->created_at->format('d M Y') }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Booking Graph Section -->
    <div class="py-16 bg-gray-50">
        <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="p-6 bg-white rounded-xl">
                <h3 class="text-lg font-semibold text-gray-900">Statistik Pemesanan</h3>
                <div class="h-64 mt-4">
                    <canvas id="bookingChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div class="py-20 bg-gray-50">
        <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="text-center">
                <h2 class="text-3xl font-bold text-gray-900 sm:text-4xl">
                    Mengapa Memilih Kami?
                </h2>
                <p class="mt-4 text-lg text-gray-600">
                    Keunggulan layanan kami untuk kenyamanan perjalanan Anda
                </p>
            </div>

            <div class="mt-20">
                <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3">
                    <!-- Feature 1 -->
                    <div class="relative group">
                        <div class="absolute transition-opacity rounded-lg opacity-25 -inset-1 bg-gradient-to-r from-primary-600 to-primary-400 group-hover:opacity-50"></div>
                        <div class="relative p-6 bg-white rounded-lg">
                            <div class="flex items-center justify-center w-12 h-12 mb-4 rounded-full bg-primary-50">
                                <svg class="w-6 h-6 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900">Armada Terawat</h3>
                            <p class="mt-2 text-gray-600">Bus-bus kami selalu dalam kondisi prima dan terawat dengan baik untuk menjamin keamanan perjalanan Anda.</p>
                        </div>
                    </div>

                    <!-- Feature 2 -->
                    <div class="relative group">
                        <div class="absolute transition-opacity rounded-lg opacity-25 -inset-1 bg-gradient-to-r from-primary-600 to-primary-400 group-hover:opacity-50"></div>
                        <div class="relative p-6 bg-white rounded-lg">
                            <div class="flex items-center justify-center w-12 h-12 mb-4 rounded-full bg-primary-50">
                                <svg class="w-6 h-6 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900">Tepat Waktu</h3>
                            <p class="mt-2 text-gray-600">Kami berkomitmen untuk memberikan layanan tepat waktu sesuai dengan jadwal yang telah disepakati.</p>
                        </div>
                    </div>

                    <!-- Feature 3 -->
                    <div class="relative group">
                        <div class="absolute transition-opacity rounded-lg opacity-25 -inset-1 bg-gradient-to-r from-primary-600 to-primary-400 group-hover:opacity-50"></div>
                        <div class="relative p-6 bg-white rounded-lg">
                            <div class="flex items-center justify-center w-12 h-12 mb-4 rounded-full bg-primary-50">
                                <svg class="w-6 h-6 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900">Crew Profesional</h3>
                            <p class="mt-2 text-gray-600">Tim crew kami telah terlatih dan berpengalaman untuk memberikan pelayanan terbaik.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Call to Action -->
    <div class="bg-primary-600">
        <div class="px-4 py-12 mx-auto max-w-7xl sm:px-6 lg:py-16 lg:px-8 lg:flex lg:items-center lg:justify-between">
            <h2 class="text-3xl font-bold tracking-tight text-white sm:text-4xl">
                <span class="block">Siap untuk memulai perjalanan?</span>
                <span class="block text-primary-200">Pesan bus Anda sekarang.</span>
            </h2>
            <div class="flex mt-8 lg:mt-0 lg:flex-shrink-0">
                @auth
                    <div class="inline-flex rounded-lg shadow">
                        <a href="{{ route('filament.panel.resources.bookings.create') }}"
                           class="inline-flex items-center justify-center px-5 py-3 text-base font-medium transition-colors bg-white border border-transparent rounded-lg text-primary-600 hover:bg-primary-50">
                            Pesan Sekarang
                        </a>
                    </div>
                @else
                    <div class="inline-flex rounded-lg shadow">
                        <a href="/panel/register"
                           class="inline-flex items-center justify-center px-5 py-3 text-base font-medium transition-colors bg-white border border-transparent rounded-lg text-primary-600 hover:bg-primary-50">
                            Daftar & Pesan
                        </a>
                    </div>
                @endauth
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-white">
        <div class="px-4 py-12 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 gap-8 pt-8 border-t border-gray-200 sm:grid-cols-2 lg:grid-cols-4">
                <div>
                    <h3 class="text-sm font-semibold tracking-wider text-gray-400 uppercase">Tentang Kami</h3>
                    <p class="mt-4 text-base text-gray-500">
                        {{ config('app.name') }} adalah layanan sewa bus profesional dengan pengalaman lebih dari 10 tahun melayani berbagai kebutuhan transportasi.
                    </p>
                </div>
                <div>
                    <h3 class="text-sm font-semibold tracking-wider text-gray-400 uppercase">Kontak</h3>
                    <ul class="mt-4 space-y-4">
                        <li class="text-base text-gray-500">
                            <p class="flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                </svg>
                                (021) 123-4567
                            </p>
                        </li>
                        <li class="text-base text-gray-500">
                            <p class="flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                info@bookingbus.com
                            </p>
                        </li>
                        <li class="text-base text-gray-500">
                            <p class="flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                Jl. Contoh No. 123, Semarang Selatan
                            </p>
                        </li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-sm font-semibold tracking-wider text-gray-400 uppercase">Layanan</h3>
                    <ul class="mt-4 space-y-4">
                        <li>
                            <a href="#" class="text-base text-gray-500 hover:text-gray-900">
                                Sewa Bus Pariwisata
                            </a>
                        </li>
                        <li>
                            <a href="#" class="text-base text-gray-500 hover:text-gray-900">
                                Antar Jemput Karyawan
                            </a>
                        </li>
                        <li>
                            <a href="#" class="text-base text-gray-500 hover:text-gray-900">
                                City Tour
                            </a>
                        </li>
                        <li>
                            <a href="#" class="text-base text-gray-500 hover:text-gray-900">
                                Study Tour
                            </a>
                        </li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-sm font-semibold tracking-wider text-gray-400 uppercase">Legal</h3>
                    <ul class="mt-4 space-y-4">
                        <li>
                            <a href="#" class="text-base text-gray-500 hover:text-gray-900">
                                Syarat & Ketentuan
                            </a>
                        </li>
                        <li>
                            <a href="#" class="text-base text-gray-500 hover:text-gray-900">
                                Kebijakan Privasi
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="pt-8 mt-8 text-center border-t border-gray-200">
                <p class="text-base text-gray-400">&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const months = {!! json_encode(collect(range(1, 12))->map(fn($month) => date('F', mktime(0, 0, 0, $month, 1)))) !!};
            const bookingData = {!! json_encode(collect(range(1, 12))->map(fn($month) => \App\Models\Booking::whereMonth('created_at', $month)->whereYear('created_at', date('Y'))->count())) !!};

            new Chart(document.getElementById('bookingChart'), {
                type: 'line',
                data: {
                    labels: months,
                    datasets: [{
                        label: 'Jumlah Pemesanan',
                        data: bookingData,
                        borderColor: '#2563eb',
                        backgroundColor: 'rgba(37, 99, 235, 0.1)',
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    }
                }
            });
        });
    </script>
</x-app-layout>
