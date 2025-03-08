<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Kwitansi #{{ $booking->id }}</title>
    <style>
        @page {
            margin: 40px;
        }
        body {
            font-family: 'Helvetica', sans-serif;
            margin: 0;
            padding: 0;
            color: #111827;
        }
        .container {
            padding: 20px;  /* Kurangi dari 40px */
            position: relative;
        }
        .header {
            margin-bottom: 30px;  /* Kurangi dari 60px */
        }
        .company-info {
            float: left;
        }
        .company-info h1 {
            font-size: 24px;
            margin: 0 0 10px 0;
        }
        .company-info p {
            color: #4B5563;
            margin: 5px 0;
            font-size: 14px;
        }
        .receipt-info {
            float: right;
            text-align: right;
        }
        .receipt-badge {
            display: inline-block;
            background: #2563eb;
            color: white;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 14px;
            margin-bottom: 16px;
        }
        .receipt-number {
            font-size: 24px;
            font-weight: bold;
            margin: 0;
        }
        .receipt-date {
            color: #4B5563;
            margin: 5px 0;
        }
        .clear {
            clear: both;
        }
        .customer-info {
            background: #F9FAFB;
            border: 1px solid #F3F4F6;
            border-radius: 12px;
            padding: 16px;  /* Kurangi dari 24px */
            margin-bottom: 20px;  /* Kurangi dari 40px */
        }
        .customer-info h3 {
            color: #6B7280;
            font-size: 12px;
            text-transform: uppercase;
            margin: 0 0 16px 0;
        }
        .customer-name {
            font-size: 20px;
            font-weight: bold;
            margin: 0 0 12px 0;
        }
        .customer-details {
            color: #4B5563;
            font-size: 14px;
            margin: 5px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th {
            background: #F9FAFB;
            padding: 12px 16px;
            text-align: left;
            font-size: 12px;
            text-transform: uppercase;
            color: #6B7280;
        }
        td {
            padding: 8px 16px;  /* Kurangi padding vertical */
            border-bottom: 1px solid #F3F4F6;
        }
        .item-name {
            font-size: 16px;
            font-weight: bold;
        }
        .item-details {
            color: #4B5563;
            font-size: 14px;
            margin: 2px 0;  /* Kurangi dari 5px */
        }
        .total-row td {
            border-top: 2px solid #111827;
            font-size: 20px;
            font-weight: bold;
            padding-top: 20px;
        }
        .amount-in-words {
            background: #EFF6FF;
            border: 1px solid #BFDBFE;
            border-radius: 8px;
            padding: 16px;
            color: #1E40AF;
            font-size: 14px;
            margin: 30px 0;
        }
        .signature {
            text-align: right;
            margin-top: 60px;
            padding-top: 30px;
            border-top: 1px solid #E5E7EB;
        }
        .signature p {
            margin: 5px 0;
        }
        .signature .sign-line {
            margin: 50px 0 10px 0;
            border-top: 1px solid #111827;
            width: 200px;
            display: inline-block;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="company-info">
                <h1>{{ config('app.name') }}</h1>
                <p>Jl. Contoh No. 123, Semarang Selatan</p>
                <p>Indonesia 12345</p>
                <p>Tel: (021) 123-4567</p>
                <p>Email: info@bookingbus.com</p>
            </div>
            <div class="receipt-info">
                <div class="receipt-badge">KWITANSI</div>
                <p class="receipt-number">#{{ str_pad($booking->id, 5, '0', STR_PAD_LEFT) }}</p>
                <p class="receipt-date">{{ $booking->created_at->format('d/m/Y') }}</p>
            </div>
            <div class="clear"></div>
        </div>

        <div class="customer-info">
            <h3>Telah diterima dari:</h3>
            <p class="customer-name">{{ $booking->customer->name }}</p>
            <p class="customer-details">{{ $booking->customer->address ?: '-' }}</p>
            <p class="customer-details">Tel: {{ $booking->customer->phone ?: '-' }}</p>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Deskripsi</th>
                    <th style="text-align: right">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <p class="item-name">Sewa Bus {{ $booking->bus->name }}</p>
                        @php
                            if ($booking->bus->pricing_type === 'daily') {
                                $days = $booking->return_date
                                    ? ceil($booking->booking_date->floatDiffInDays($booking->return_date))
                                    : 1;
                            }
                        @endphp

                        <div style="margin-top: 8px;">  <!-- Kurangi dari 16px -->
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-bottom: 4px;">  <!-- Kurangi gap dan margin -->
                                <div>
                                    <p style="font-size: 12px; color: #6B7280; margin-bottom: 2px;">Lokasi Penjemputan</p>
                                    <p style="margin-top: 2px; color: #111827;">{{ $booking->pickup_location }}</p>
                                </div>
                                <div>
                                    <p style="font-size: 12px; color: #6B7280; margin-bottom: 2px;">Tujuan</p>
                                    <p style="margin-top: 2px; color: #111827;">{{ $booking->destination }}</p>
                                </div>
                            </div>

                            <div style="margin-bottom: 4px;">  <!-- Kurangi dari 16px -->
                                <p style="font-size: 12px; color: #6B7280; margin-bottom: 2px;">Waktu</p>
                                <p style="margin-top: 2px; color: #111827;">
                                    {{ $booking->booking_date->format('d M Y H:i') }}
                                    @if($booking->return_date)
                                        - {{ $booking->return_date->format('d M Y H:i') }}
                                    @endif
                                </p>
                            </div>

                            @if($booking->special_requests)
                                <div style="margin-bottom: 4px;">  <!-- Kurangi dari 16px -->
                                    <p style="font-size: 12px; color: #6B7280; margin-bottom: 2px;">Catatan</p>
                                    <p style="margin-top: 2px; color: #111827;">{{ $booking->special_requests }}</p>
                                </div>
                            @endif

                            <div style="margin-top: 4px;">
                                @if($booking->bus->pricing_type === 'daily')
                                    <p style="color: #4B5563;">
                                        {{ $days }} hari × Rp {{ number_format($booking->bus->price_per_day, 0, ',', '.') }}/hari
                                    </p>
                                @else
                                    <p style="color: #4B5563;">
                                        Estimasi 100 km × Rp {{ number_format($booking->bus->price_per_km, 0, ',', '.') }}/km
                                    </p>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td style="text-align: right">
                        @if($booking->bus->pricing_type === 'daily')
                            Rp {{ number_format($booking->bus->price_per_day * $days, 0, ',', '.') }}
                        @else
                            Rp {{ number_format($booking->bus->price_per_km * 100, 0, ',', '.') }}
                        @endif
                    </td>
                </tr>
                @if($booking->seat_type === 'legrest')
                <tr>
                    <td>
                        <p class="item-details">Tambahan Kursi Legrest ({{ $booking->total_seats }} kursi × Rp {{ number_format($booking->bus->legrest_price_per_seat, 0, ',', '.') }})</p>
                    </td>
                    <td style="text-align: right">
                        Rp {{ number_format($booking->bus->legrest_price_per_seat * $booking->total_seats, 0, ',', '.') }}
                    </td>
                </tr>
                @endif
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td style="text-align: right">Total</td>
                    <td style="text-align: right">
                        Rp {{ number_format($booking->total_amount, 0, ',', '.') }}
                    </td>
                </tr>
            </tfoot>
        </table>

        <div class="amount-in-words">
            Terbilang: {{ ucwords(\App\Helpers\Terbilang::make($booking->total_amount)) }} Rupiah
        </div>

        <!-- Payment Info -->
        <div class="payment-info">
            <h3 style="margin-bottom: 16px; color: #374151; font-size: 16px; font-weight: 600;">Informasi Pembayaran</h3>
            <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
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

                <tr>
                    <td style="padding: 8px 0; width: 200px;"><strong>Status Pembayaran:</strong></td>
                    <td>{{ ucfirst($booking->payment_status) }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0;"><strong>Metode Pembayaran:</strong></td>
                    <td>{{ $details['payment_type'] ?? '-' }}</td>
                </tr>
                {{-- <tr>
                    <td style="padding: 8px 0;"><strong>Virtual Account:</strong></td>
                    <td>{{ $vaNumber }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0;"><strong>Bank:</strong></td>
                    <td>{{ $bank }}</td>
                </tr> --}}
                <tr>
                    <td style="padding: 8px 0;"><strong>Waktu Transaksi:</strong></td>
                    <td>{{ $transactionTime }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0;"><strong>Waktu Pembayaran:</strong></td>
                    <td>{{ $settlementTime }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0;"><strong>ID Transaksi:</strong></td>
                    <td>{{ $details['transaction_id'] ?? '-' }}</td>
                </tr>
                {{-- <tr>
                    <td style="padding: 8px 0;"><strong>Total:</strong></td>
                    <td>Rp {{ number_format($details['gross_amount'] ?? $booking->total_amount, 0, ',', '.') }}</td>
                </tr> --}}
            </table>
        </div>

        <div class="signature">
            <p>Hormat Kami,</p>
            <div class="sign-line"></div>
            <p>{{ config('app.name') }}</p>
        </div>
    </div>
</body>
</html>
