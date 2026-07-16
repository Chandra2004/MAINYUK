<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $booking->booking_code }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        @page {
            margin: 40px 50px 80px 50px; /* Atas Kanan Bawah Kiri */
        }
        body { 
            font-family: 'Montserrat', 'DejaVu Sans', sans-serif; 
            font-size: 13px; 
            color: #1a1a2e; 
            background: #fff; 
            line-height: 1.5;
            margin: 0;
            padding: 0;
        }
        h1, h2, h3, h4, p, div, table, tr, td, th {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        .header { 
            border-bottom: 3px solid #1E3932;
            padding-bottom: 15px;
            margin-bottom: 30px;
            width: 100%;
        }
        table.layout-table {
            width: 100%;
            border-collapse: collapse;
        }
        table.layout-table td {
            vertical-align: top;
        }
        .logo { 
            font-size: 32px; 
            font-weight: 800; 
            color: #1E3932;
        }
        .logo span { color: #cba258; }
        .header-right { 
            text-align: right; 
        }
        .header-right h1 {
            font-size: 28px;
            font-weight: 800;
            color: #1E3932;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 5px;
        }
        .header-right p {
            color: #666;
            font-size: 12px;
            font-weight: 500;
        }
        
        .invoice-details {
            margin-bottom: 40px;
            width: 100%;
        }
        .invoice-details td {
            padding-bottom: 5px;
        }
        .detail-label {
            font-weight: 700;
            color: #888;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .detail-val {
            font-weight: 600;
            font-size: 13px;
            color: #1a1a2e;
        }

        .section-title { 
            font-size: 14px; 
            font-weight: 800; 
            text-transform: uppercase; 
            letter-spacing: 1px; 
            color: #1E3932; 
            border-bottom: 2px solid #e2e8f0; 
            padding-bottom: 8px; 
            margin-bottom: 15px; 
            margin-top: 30px;
        }

        .info-grid {
            width: 100%;
            margin-bottom: 20px;
        }
        .info-grid td {
            width: 50%;
            padding: 10px 0;
            border-bottom: 1px dashed #f0f0f0;
        }
        
        .table-items { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 20px; 
        }
        .table-items th { 
            background: #1E3932; 
            text-align: left; 
            padding: 12px 15px; 
            font-size: 11px; 
            font-weight: 700; 
            text-transform: uppercase; 
            color: #ffffff; 
            letter-spacing: 1px;
        }
        .table-items td { 
            padding: 15px; 
            border-bottom: 1px solid #e2e8f0; 
            font-size: 13px; 
            font-weight: 500;
        }
        
        .totals-wrapper {
            width: 100%;
            margin-top: 30px;
        }
        .totals-table {
            width: 50%;
            float: right;
            border-collapse: collapse;
        }
        .totals-table td {
            padding: 8px 0;
            font-size: 13px;
        }
        .totals-table .label {
            color: #666;
            font-weight: 600;
        }
        .totals-table .amount {
            text-align: right;
            font-weight: 700;
        }
        .grand-total {
            background: #f8fafc;
            border-top: 2px solid #1E3932;
        }
        .grand-total .label {
            color: #1E3932;
            font-size: 16px;
            font-weight: 800;
            padding: 15px 10px;
        }
        .grand-total .amount {
            color: #1E3932;
            font-size: 18px;
            font-weight: 800;
            padding: 15px 10px;
        }

        .badge { 
            display: inline-block; 
            padding: 4px 12px; 
            border-radius: 4px; 
            font-size: 11px; 
            font-weight: 700; 
            text-transform: uppercase;
        }
        .badge-active { background: #d1fae5; color: #065f46; border: 1px solid #34d399; }
        .badge-pending { background: #fef3c7; color: #92400e; border: 1px solid #fbbf24; }
        .badge-cancelled { background: #fee2e2; color: #991b1b; border: 1px solid #f87171; }
        
        .footer { 
            position: fixed;
            bottom: 0px;
            left: 0px;
            right: 0px;
            text-align: center; 
            padding-top: 15px; 
            border-top: 1px solid #e2e8f0; 
            font-size: 10px; 
            color: #888; 
            font-weight: 500;
        }
        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <table class="layout-table">
            <tr>
                <td style="width: 60%;">
                    <div class="logo">Main<span>Yuk</span></div>
                    <div style="margin-top: 10px; color: #666; font-size: 11px; font-weight: 500;">
                        Platform Booking Lapangan Olahraga<br>
                        Jl. Olahraga No. 1, Jakarta Selatan
                    </div>
                </td>
                <td class="header-right" style="width: 40%;">
                    <h1>INVOICE</h1>
                    <p>#{{ $booking->booking_code }}</p>
                    <p>Tanggal: {{ \Carbon\Carbon::parse($booking->created_at)->translatedFormat('d F Y') }}</p>
                </td>
            </tr>
        </table>
    </div>

    <!-- Billing Info -->
    <table class="layout-table invoice-details">
        <tr>
            <td style="width: 50%;">
                <div class="detail-label">DITAGIHKAN KEPADA:</div>
                <div class="detail-val" style="font-size: 16px; margin-top: 5px;">{{ $user->name }}</div>
                <div class="detail-val" style="color: #666; font-weight: 500;">{{ $user->email }}</div>
                <div class="detail-val" style="color: #666; font-weight: 500;">{{ $user->phone ?? '-' }}</div>
            </td>
            <td style="width: 50%; text-align: right;">
                <div class="detail-label">STATUS PEMBAYARAN:</div>
                <div style="margin-top: 8px;">
                    <span class="badge badge-{{ $booking->status }}">
                        {{ $booking->status === 'active' ? 'LUNAS / BERHASIL' : strtoupper($booking->status) }}
                    </span>
                </div>
                <div style="margin-top: 10px;">
                    <span class="detail-label">METODE:</span> 
                    <span class="detail-val">{{ strtoupper($booking->payment_method ?? 'MIDTRANS') }}</span>
                </div>
            </td>
        </tr>
    </table>

    <!-- Booking Detail -->
    <div class="section-title">Informasi Pesanan Lapangan</div>
    <table class="layout-table info-grid">
        <tr>
            <td>
                <div class="detail-label">NAMA LAPANGAN</div>
                <div class="detail-val">{{ $court->name }} ({{ $booking->court_detail ?? 'Semua Area' }})</div>
            </td>
            <td>
                <div class="detail-label">JENIS OLAHRAGA</div>
                <div class="detail-val">{{ ucfirst($court->sport_type) }}</div>
            </td>
        </tr>
        <tr>
            <td>
                <div class="detail-label">JADWAL BOOKING</div>
                <div class="detail-val">{{ \Carbon\Carbon::parse($booking->date)->translatedFormat('l, d F Y') }}</div>
            </td>
            <td>
                <div class="detail-label">JAM BERMAIN</div>
                <div class="detail-val">{{ $booking->time_start }} – {{ $booking->time_end }} ({{ $booking->duration_hours }} Jam)</div>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <div class="detail-label">ALAMAT LAPANGAN</div>
                <div class="detail-val">{{ $court->address }}, {{ $court->city }}</div>
            </td>
        </tr>
    </table>

    <!-- Items Table -->
    <table class="table-items">
        <thead>
            <tr>
                <th style="width: 50%;">DESKRIPSI ITEM</th>
                <th style="width: 15%; text-align: center;">DURASI</th>
                <th style="width: 35%; text-align: right;">JUMLAH (IDR)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <strong style="color: #1E3932;">Sewa Lapangan Olahraga</strong><br>
                    <span style="font-size: 11px; color: #666; font-weight: 400;">Tarif per jam: Rp {{ number_format($court->price_per_hour, 0, ',', '.') }}</span>
                </td>
                <td style="text-align: center;">{{ $booking->duration_hours }} Jam</td>
                <td style="text-align: right;">Rp {{ number_format($court->price_per_hour * $booking->duration_hours, 0, ',', '.') }}</td>
            </tr>
            @if($booking->addon_total > 0)
            <tr>
                <td>
                    <strong style="color: #1E3932;">Penyewaan Alat (Equipment)</strong><br>
                    <span style="font-size: 11px; color: #666; font-weight: 400;">Berbagai peralatan ekstra</span>
                </td>
                <td style="text-align: center;">-</td>
                <td style="text-align: right;">Rp {{ number_format($booking->addon_total, 0, ',', '.') }}</td>
            </tr>
            @endif
            <tr>
                <td>
                    <strong style="color: #1E3932;">Biaya Layanan Aplikasi</strong>
                </td>
                <td style="text-align: center;">-</td>
                <td style="text-align: right;">Rp {{ number_format(round(($court->price_per_hour * $booking->duration_hours) * 0.05), 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Totals -->
    <div class="totals-wrapper clearfix">
        <table class="totals-table">
            <tr>
                <td class="label">Subtotal</td>
                <td class="amount">Rp {{ number_format($booking->subtotal, 0, ',', '.') }}</td>
            </tr>
            @if($booking->discount > 0)
            <tr>
                <td class="label">Diskon Promo</td>
                <td class="amount" style="color: #059669;">- Rp {{ number_format($booking->discount, 0, ',', '.') }}</td>
            </tr>
            @endif
            <tr class="grand-total">
                <td class="label">TOTAL BAYAR</td>
                <td class="amount">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    <!-- Footer -->
    <div class="footer">
        Dokumen ini sah dan diterbitkan secara otomatis oleh sistem MainYuk.<br>
        Untuk bantuan lebih lanjut, hubungi kami di <strong>support@mainyuk.id</strong> atau kunjungi <strong>{{ config('app.url') }}</strong>.<br><br>
        <strong>© 2026 MainYuk — Platform Booking Lapangan Olahraga Terpercaya</strong>
    </div>
</body>
</html>
