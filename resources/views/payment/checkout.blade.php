<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pembayaran #{{ $booking->id }} - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        * {
            font-family: 'Inter', sans-serif;
        }
    </style>
    <!-- Ganti script src Midtrans sesuai environment -->
    <script type="text/javascript"
        src="{{ config('services.midtrans.snap_js_url') }}"
        data-client-key="{{ config('services.midtrans.client_key') }}">
    </script>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen">
        {{-- Header with Back Button --}}
        <header class="mb-6 bg-white shadow-sm">
            <div class="flex items-center justify-between px-4 py-4 mx-auto max-w-7xl">
                <h1 class="text-2xl font-bold text-gray-900">Checkout Pembayaran</h1>
                <a href="{{ auth()->user()->role === 'customer' ? route('customer.bookings.index') : route('filament.panel.resources.bookings.index') }}"
                   class="inline-flex items-center px-4 py-2 text-gray-700 transition-colors bg-gray-100 rounded-lg hover:bg-gray-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Tunda Pembayaran
                </a>
            </div>
        </header>

        <main class="px-4 mx-auto max-w-7xl">
            <div class="grid gap-8 lg:grid-cols-2">
                {{-- Order Summary --}}
                <div class="space-y-6">
                    {{-- Bus Details Card --}}
                    <div class="overflow-hidden bg-white shadow-sm rounded-xl">
                        <div class="p-6">
                            <h2 class="mb-4 text-lg font-semibold">Detail Bus</h2>
                            <div class="flex gap-4">
                                @if($booking->bus->images && count($booking->bus->images) > 0)
                                    <img src="{{ Storage::url($booking->bus->images[0]) }}"
                                         alt="{{ $booking->bus->name }}"
                                         class="object-cover w-24 h-24 rounded-lg">
                                @endif
                                <div>
                                    <h3 class="font-medium">{{ $booking->bus->name }}</h3>
                                    <p class="text-sm text-gray-600">{{ $booking->bus->number_plate }}</p>
                                    <p class="mt-2 text-sm">{{ $booking->total_seats }} Kursi ({{ $booking->seat_type }})</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Travel Details Card --}}
                    <div class="bg-white shadow-sm rounded-xl">
                        <div class="p-6">
                            <h2 class="mb-4 text-lg font-semibold">Detail Perjalanan</h2>
                            <div class="grid gap-4">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="text-sm text-gray-600">Tanggal Berangkat</label>
                                        <p class="font-medium">{{ $booking->booking_date->format('d M Y H:i') }}</p>
                                    </div>
                                    @if($booking->return_date)
                                        <div>
                                            <label class="text-sm text-gray-600">Tanggal Kembali</label>
                                            <p class="font-medium">{{ $booking->return_date->format('d M Y H:i') }}</p>
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <label class="text-sm text-gray-600">Lokasi Jemput</label>
                                    <p class="font-medium">{{ $booking->pickup_location }}</p>
                                </div>
                                <div>
                                    <label class="text-sm text-gray-600">Tujuan</label>
                                    <p class="font-medium">{{ $booking->destination }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Total Amount Card --}}
                    <div class="bg-white shadow-sm rounded-xl">
                        <div class="p-6">
                            <h2 class="mb-4 text-lg font-semibold">Ringkasan Pembayaran</h2>
                            <div class="pb-4 space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Total</span>
                                    <span class="text-xl font-bold text-primary-600">
                                        Rp {{ number_format($booking->total_amount) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Payment Frame --}}
                <div class="bg-white shadow-sm payment-frame-container rounded-xl">
                    <div id="snap-container"></div>
                </div>
            </div>

            {{-- Add Back Button at the Bottom for Mobile --}}
            <div class="mt-6 lg:hidden">
                <a href="{{ auth()->user()->role === 'customer' ? route('customer.bookings.index') : route('filament.panel.resources.bookings.index') }}"
                   class="block w-full px-4 py-3 text-center text-gray-700 transition-colors bg-gray-100 rounded-lg hover:bg-gray-200">
                    Tunda Pembayaran & Kembali ke Daftar Booking
                </a>
            </div>
        </main>
    </div>

    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function() {
            const snapToken = '{{ $booking->snap_token }}';
            console.log('Using snap token:', snapToken);

            if (!snapToken) {
                alert('Token pembayaran tidak valid. Silakan coba lagi.');
                window.location.href = '{{ auth()->user()->role === 'customer' ? route('customer.bookings.index') : route('filament.panel.resources.bookings.index') }}';
                return;
            }

            try {
                window.snap.embed(snapToken, {
                    embedId: 'snap-container',
                    onSuccess: function(result) {
                        console.log('Payment success:', result);
                        window.location.href = '{{ route("payment.success") }}' +
                            '?order_id=' + result.order_id +
                            '&status=' + result.transaction_status;
                    },
                    onPending: function(result) {
                        console.log('Payment pending:', result);
                        window.location.href = '{{ route("payment.pending") }}' +
                            '?order_id=' + result.order_id;
                    },
                    onError: function(result) {
                        console.error('Payment Error:', result);
                        // Handle expired payment
                        if (result.status_code === '407' || result.transaction_status === 'expire') {
                            fetch('{{ route("payment.update-status", $booking->id) }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({
                                    status: 'expired',
                                    payment_status: 'cancelled'
                                })
                            }).then(response => response.json())
                              .then(data => {
                                  if (data.success) {
                                      window.location.href = '{{ route("payment.cancelled") }}';
                                  }
                              });
                        } else {
                            window.location.href = '{{ route("payment.error") }}';
                        }
                    },
                    onClose: function() {
                        // Redirect ke halaman booking list jika user menutup modal
                        window.location.href = '{{ auth()->user()->role === 'customer' ? route('customer.bookings.index') : route('filament.panel.resources.bookings.index') }}';
                    }
                });
            } catch (e) {
                console.error('Snap initialization error:', e);
                alert('Terjadi kesalahan saat memuat pembayaran. Silakan coba lagi.');
                window.location.href = '{{ route("filament.panel.resources.bookings.index") }}';
            }
        });
    </script>
</body>
</html>
