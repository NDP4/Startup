<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Review Booking #{{ $booking->id }} - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .rating-container {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem;
            border-radius: 0.5rem;
            background-color: #f8fafc;
        }
        .rating-stars {
            display: flex;
            gap: 0.25rem;
        }
        .rating-stars label {
            cursor: pointer;
            padding: 0.25rem;
        }
        .rating-stars input {
            display: none;
        }
        .rating-stars svg {
            width: 2rem;
            height: 2rem;
            fill: #e2e8f0;
            stroke: #cbd5e1;
            transition: all 0.2s;
        }
        .rating-stars input:checked ~ label svg,
        .rating-stars label:hover svg,
        .rating-stars label:hover ~ label svg {
            fill: #3B82F6;
            stroke: #2563EB;
        }
        .rating-text {
            font-size: 0.875rem;
            color: #64748b;
        }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-50">
    <div class="max-w-4xl px-4 py-8 mx-auto">
        <!-- Header Section -->
        <div class="mb-8 text-center">
            <h1 class="mb-2 text-3xl font-bold text-gray-900">Bagaimana Perjalanan Anda?</h1>
            <p class="text-gray-600">Bantu kami meningkatkan layanan dengan memberikan ulasan Anda</p>
        </div>

        <!-- Booking Info Card -->
        <div class="p-6 mb-8 bg-white border border-gray-100 shadow-sm rounded-xl">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Booking #{{ $booking->id }}</p>
                    <h2 class="mt-1 text-xl font-semibold text-gray-900">{{ $booking->pickup_location }} → {{ $booking->destination }}</h2>
                    <p class="mt-1 text-gray-600">{{ $booking->booking_date->format('d F Y') }}</p>
                </div>
                <div class="text-right">
                    <span class="px-3 py-1 rounded-full text-xs font-medium {{ $booking->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                        {{ ucfirst($booking->status) }}
                    </span>
                </div>
            </div>
        </div>

        <form action="{{ route('reviews.store', $booking) }}" method="POST" class="space-y-6">
            @csrf

            <!-- Bus Review Section -->
            <div class="overflow-hidden bg-white border border-gray-100 shadow-sm rounded-xl">
                <div class="p-6">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="flex items-center justify-center w-12 h-12 rounded-full bg-amber-100">
                            <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 4H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-2m-4-1v8m0 0l3-3m-3 3L9 8m-5 5h2.586a1 1 0 01.707.293l2.414 2.414a1 1 0 00.707.293h3.172a1 1 0 00.707-.293l2.414-2.414a1 1 0 01.707-.293H20" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Review Bus</h3>
                            <p class="text-sm text-gray-600">{{ $booking->bus->name }}</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-700">Berikan Rating</label>
                            <div class="rating-container">
                                <div class="rating-stars">
                                    @for($i = 5; $i >= 1; $i--)
                                        <input type="radio" id="bus-star{{ $i }}" name="bus_rating" value="{{ $i }}" required />
                                        <label for="bus-star{{ $i }}">
                                            <svg viewBox="0 0 24 24">
                                                <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
                                            </svg>
                                        </label>
                                    @endfor
                                </div>
                                <span class="rating-text">Pilih rating</span>
                            </div>
                            @error('bus_rating')
                                <p class="mt-1 text-sm text-red-600">{{ $errors->first('bus_rating') }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-700">Ceritakan Pengalaman Anda</label>
                            <textarea
                                name="bus_comment"
                                rows="3"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                                placeholder="Bagaimana kondisi bus? Apakah nyaman dan bersih?"
                                required
                            ></textarea>
                            @error('bus_comment')
                                <p class="mt-1 text-sm text-red-600">{{ $errors->first('bus_comment') }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Crew Review Section -->
            <div class="overflow-hidden bg-white border border-gray-100 shadow-sm rounded-xl">
                <div class="p-6">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="flex items-center justify-center w-12 h-12 rounded-full bg-amber-100">
                            <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Review Crew</h3>
                            @if($booking->crewAssignments->isNotEmpty())
                                <p class="text-sm text-gray-600">{{ $booking->crewAssignments->first()->crew->name }}</p>
                            @else
                                <p class="text-sm text-gray-600">Tidak ada crew yang ditugaskan</p>
                            @endif
                        </div>
                    </div>

                    @if($booking->crewAssignments->isNotEmpty())
                        <div class="space-y-4">
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-700">Berikan Rating</label>
                                <div class="rating-container">
                                    <div class="rating-stars">
                                        @for($i = 5; $i >= 1; $i--)
                                            <input type="radio" id="crew-star{{ $i }}" name="crew_rating" value="{{ $i }}" required />
                                            <label for="crew-star{{ $i }}">
                                                <svg viewBox="0 0 24 24">
                                                    <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
                                                </svg>
                                            </label>
                                        @endfor
                                    </div>
                                    <span class="rating-text">Pilih rating</span>
                                </div>
                                @error('crew_rating')
                                    <p class="mt-1 text-sm text-red-600">{{ $errors->first('crew_rating') }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-700">Ceritakan Pengalaman Anda</label>
                                <textarea
                                    name="crew_comment"
                                    rows="3"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                                    placeholder="Bagaimana pelayanan crew? Apakah ramah dan profesional?"
                                    required
                                ></textarea>
                                @error('crew_comment')
                                    <p class="mt-1 text-sm text-red-600">{{ $errors->first('crew_comment') }}</p>
                                @enderror
                            </div>
                        </div>
                    @else
                        <div class="p-4 text-sm rounded-lg bg-amber-50 text-amber-800">
                            <p>Tidak dapat memberikan review crew karena tidak ada crew yang ditugaskan pada booking ini.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end gap-3">
                <a href="{{ route('filament.panel.resources.bookings.index') }}"
                    class="px-6 py-2 text-gray-700 transition-colors duration-200 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                    Batal
                </a>
                <button type="submit"
                        class="px-6 py-2 text-white transition-colors duration-200 rounded-lg bg-blue-600 hover:bg-blue-700">
                    Kirim Review
                </button>
            </div>
        </form>
    </div>

    <script>
        // Update rating text when star is selected
        document.querySelectorAll('.rating-stars input').forEach(input => {
            input.addEventListener('change', (e) => {
                const container = e.target.closest('.rating-container');
                const text = container.querySelector('.rating-text');
                text.textContent = `${e.target.value} dari 5 bintang`;
            });
        });
    </script>
</body>
</html>
