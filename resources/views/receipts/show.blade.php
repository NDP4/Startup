<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kwitansi #{{ $booking->id }} - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#fff7ed',
                            100: '#ffedd5',
                            200: '#fed7aa',
                            300: '#fdba74',
                            400: '#fb923c',
                            500: '#f97316',
                            600: '#ea580c',
                            700: '#c2410c',
                            800: '#9a3412',
                            900: '#7c2d12',
                        },
                    }
                }
            }
        }
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        * {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen p-6">
        <div class="max-w-4xl mx-auto">
            {{-- Header with Actions --}}
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-bold text-gray-900">Kwitansi Pembayaran</h1>
                <div class="space-x-2">
                    <a href="{{ route('booking.receipt.download', $booking) }}"
                       class="inline-flex items-center px-4 py-2 text-white transition-colors duration-200 rounded-lg bg-blue-600 hover:bg-blue-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Unduh PDF
                    </a>
                    <a href="{{ route('filament.panel.resources.bookings.index') }}"
                       class="inline-flex items-center px-4 py-2 text-gray-700 transition-colors duration-200 bg-gray-100 rounded-lg hover:bg-gray-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Kembali
                    </a>
                </div>
            </div>

            {{-- Receipt Card --}}
            <div class="p-8 bg-white shadow-lg rounded-2xl">
                {{-- Company Info --}}
                <div class="flex items-start justify-between mb-12">
                    <div>
                        <h2 class="mb-1 text-3xl font-bold text-gray-900">{{ config('app.name') }}</h2>
                        <div class="space-y-1 text-gray-600">
                            <p>Jl. Contoh No. 123, Semarang Selatan</p>
                            <p>Indonesia 12345</p>
                            <p>Tel: (021) 123-4567</p>
                            <p>Email: info@bookingbus.com</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="px-4 py-2 mb-4 text-sm font-medium text-white rounded-lg bg-blue-600">KWITANSI</div>
                        <p class="mb-1 text-2xl font-bold text-gray-900">#{{ str_pad($booking->id, 5, '0', STR_PAD_LEFT) }}</p>
                        <p class="text-gray-600">{{ $booking->created_at->format('d F Y') }}</p>
                    </div>
                </div>

                {{-- Customer Info --}}
                <div class="p-6 mb-8 border border-gray-100 rounded-xl bg-gray-50">
                    <h3 class="mb-4 text-sm font-medium text-gray-500">CUSTOMER</h3>
                    <p class="mb-2 text-xl font-bold text-gray-900">{{ $booking->customer->name }}</p>
                    <div class="space-y-1 text-gray-600">
                        <p>{{ $booking->customer->address ?: '-' }}</p>
                        <p>Tel: {{ $booking->customer->phone ?: '-' }}</p>
                        <p>Email: {{ $booking->customer->email }}</p>
                    </div>
                </div>

                {{-- Payment Details --}}
                <div class="mb-8">
                    <table class="w-full">
                        <thead>
                            <tr>
                                <th class="px-4 py-3 text-sm font-medium tracking-wider text-left text-gray-500 uppercase bg-gray-50">Deskripsi</th>
                                <th class="px-4 py-3 text-sm font-medium tracking-wider text-right text-gray-500 uppercase bg-gray-50">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <tr>
                                <td class="px-4 py-4">
                                    <p class="text-lg font-semibold text-gray-900">Sewa Bus {{ $booking->bus->name }}</p>
                                    <div class="mt-4 space-y-2">
                                        @php
                                            if ($booking->bus->pricing_type === 'daily') {
                                                $days = $booking->return_date
                                                    ? ceil($booking->booking_date->floatDiffInDays($booking->return_date))
                                                    : 1;
                                            }
                                        @endphp

                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <p class="text-sm text-gray-500">Lokasi Penjemputan</p>
                                                <p class="text-gray-900">{{ $booking->pickup_location }}</p>
                                            </div>
                                            <div>
                                                <p class="text-sm text-gray-500">Tujuan</p>
                                                <p class="text-gray-900">{{ $booking->destination }}</p>
                                            </div>
                                        </div>

                                        <div>
                                            <p class="text-sm text-gray-500">Waktu</p>
                                            <p class="text-gray-900">
                                                {{ $booking->booking_date->format('d M Y H:i') }}
                                                @if($booking->return_date)
                                                    - {{ $booking->return_date->format('d M Y H:i') }}
                                                @endif
                                            </p>
                                        </div>

                                        @if($booking->special_requests)
                                            <div>
                                                <p class="text-sm text-gray-500">Catatan</p>
                                                <p class="text-gray-900">{{ $booking->special_requests }}</p>
                                            </div>
                                        @endif

                                        @if($booking->bus->pricing_type === 'daily')
                                            <p class="mt-2 text-gray-600">
                                                {{ $days }} hari × Rp {{ number_format($booking->bus->price_per_day, 0, ',', '.') }}/hari
                                            </p>
                                        @else
                                            <p class="mt-2 text-gray-600">
                                                Estimasi 100 km × Rp {{ number_format($booking->bus->price_per_km, 0, ',', '.') }}/km
                                            </p>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-4 text-right">
                                    <span class="text-lg font-semibold text-gray-900">
                                        @if($booking->bus->pricing_type === 'daily')
                                            Rp {{ number_format($booking->bus->price_per_day * $days, 0, ',', '.') }}
                                        @else
                                            Rp {{ number_format($booking->bus->price_per_km * 100, 0, ',', '.') }}
                                        @endif
                                    </span>
                                </td>
                            </tr>
                            @if($booking->seat_type === 'legrest')
                            <tr>
                                <td class="px-4 py-4">
                                    <p class="text-gray-600">
                                        Tambahan Kursi Legrest ({{ $booking->total_seats }} kursi × Rp {{ number_format($booking->bus->legrest_price_per_seat, 0, ',', '.') }})
                                    </p>
                                </td>
                                <td class="px-4 py-4 text-right">
                                    <span class="text-lg font-semibold text-gray-900">
                                        Rp {{ number_format($booking->bus->legrest_price_per_seat * $booking->total_seats, 0, ',', '.') }}
                                    </span>
                                </td>
                            </tr>
                            @endif
                        </tbody>
                        <tfoot>
                            <tr class="border-t-2 border-gray-900">
                                <td class="px-4 py-4 text-right">
                                    <span class="text-sm font-medium text-gray-600">Total</span>
                                </td>
                                <td class="px-4 py-4 text-right">
                                    <span class="text-2xl font-bold text-gray-900">
                                        Rp {{ number_format($booking->total_amount, 0, ',', '.') }}
                                    </span>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                {{-- Amount in Words --}}
                <div class="p-4 mb-8 text-sm border rounded-lg border-blue-200 bg-blue-50">
                    <span class="font-medium text-blue-800">
                        Terbilang: {{ ucwords(\App\Helpers\Terbilang::make($booking->total_amount)) }} Rupiah
                    </span>
                </div>

                <!-- Payment Info -->
                <div class="p-6 mb-8 border border-gray-200 rounded-lg bg-gray-50">
                    <h4 class="mb-4 text-lg font-semibold text-gray-900">Informasi Pembayaran</h4>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        @php
                            $payment = $booking->payments->last();
                            $details = $payment?->payment_details ?? [];

                            // Get dates safely
                            $transactionTime = !empty($details['transaction_time'])
                                ? \Carbon\Carbon::parse($details['transaction_time'])->format('d M Y, H:i')
                                : (!empty($booking->created_at)
                                    ? $booking->created_at->format('d M Y, H:i')
                                    : '-');

                            $settlementTime = !empty($details['settlement_time'])
                                ? \Carbon\Carbon::parse($details['settlement_time'])->format('d M Y, H:i')
                                : (!empty($payment?->paid_at)
                                    ? $payment->paid_at->format('d M Y, H:i')
                                    : '-');

                            // Get VA number safely
                            $vaNumber = null;
                            if (!empty($details['va_numbers']) && is_array($details['va_numbers'])) {
                                $vaNumber = $details['va_numbers'][0]['va_number'] ?? null;
                            }
                            $vaNumber = $vaNumber ?? $details['va_number'] ?? '-';

                            // Get bank safely
                            $bank = null;
                            if (!empty($details['va_numbers']) && is_array($details['va_numbers'])) {
                                $bank = strtoupper($details['va_numbers'][0]['bank'] ?? '');
                            }
                            $bank = $bank ?? $details['bank'] ?? '-';
                        @endphp

                        <div>
                            <p class="text-gray-600">Status Pembayaran</p>
                            <p class="font-medium text-gray-900">{{ ucfirst($booking->payment_status) }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600">Metode Pembayaran</p>
                            <p class="font-medium text-gray-900">{{ $details['payment_type'] ?? '-' }}</p>
                        </div>
                        {{-- <div>
                            <p class="text-gray-600">Virtual Account</p>
                            <p class="font-medium text-gray-900">{{ $vaNumber }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600">Bank</p>
                            <p class="font-medium text-gray-900">{{ $bank }}</p>
                        </div> --}}
                        <div>
                            <p class="text-gray-600">Waktu Transaksi</p>
                            <p class="font-medium text-gray-900">{{ $transactionTime }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600">Waktu Pembayaran</p>
                            <p class="font-medium text-gray-900">{{ $settlementTime }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600">ID Transaksi</p>
                            <p class="font-medium text-gray-900">{{ $details['transaction_id'] ?? '-' }}</p>
                        </div>
                        {{-- <div>
                            <p class="text-gray-600">Total</p>
                            <p class="font-medium text-gray-900">Rp {{ number_format($details['gross_amount'] ?? $booking->total_amount, 0, ',', '.') }}</p>
                        </div> --}}
                    </div>
                </div>

                {{-- Signatures --}}
                <div class="flex justify-end pt-8 mt-12 border-t">
                    <div class="text-center">
                        <p class="mb-16 text-gray-600">Hormat Kami,</p>
                        <p class="font-semibold text-gray-900">{{ config('app.name') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
