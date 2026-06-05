<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Ticket AyokMain</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f4f7; color: #333333; margin: 0; padding: 20px; }
        .ticket-container { max-width: 500px; margin: 0 auto; background: #ffffff; border-radius: 24px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; }
        .ticket-header { background-color: #059669; color: #ffffff; padding: 24px; text-align: center; }
        .ticket-header h1 { margin: 0; font-size: 24px; font-weight: 800; letter-spacing: 0.5px; }
        .ticket-header p { margin: 5px 0 0 0; font-size: 14px; opacity: 0.9; }
        .ticket-body { padding: 24px; }
        .status-badge { display: inline-block; background-color: #d1fae5; color: #065f46; font-weight: bold; padding: 6px 16px; border-radius: 99px; font-size: 13px; text-transform: uppercase; margin-bottom: 20px; }
        .info-section { margin-bottom: 20px; border-bottom: 1px dashed #e2e8f0; padding-bottom: 15px; }
        .info-label { font-size: 12px; color: #718096; text-transform: uppercase; font-weight: bold; margin-bottom: 4px; }
        .info-value { font-size: 16px; color: #1a202c; font-weight: 600; }
        .venue-title { font-size: 20px; color: #059669; font-weight: 800; }
        .slot-box { background-color: #f8fafc; border-left: 4px solid #059669; padding: 10px 15px; margin-top: 8px; border-radius: 0 8px 8px 0; }
        .ticket-footer { text-align: center; padding: 20px; background-color: #fafafa; font-size: 12px; color: #a0aec0; border-top: 1px solid #edf2f7; }
    </style>
</head>
<body>

    <div class="ticket-container">
        <div class="ticket-header">
            <h1>E-TICKET AMAN</h1>
            <p>Booking ID: #AM-{{ $booking->id }}</p>
        </div>

        <div class="ticket-body">
            <center>
                <div class="status-badge">PAID / LUNAS</div>
            </center>

            <div class="info-section">
                <div class="info-label">Tempat / Gedung Olahraga</div>
                <div class="info-value venue-title">{{ $booking->venue->name }}</div>
            </div>

            <div class="info-section">
                <div class="info-label">Nama Pemesan</div>
                <div class="info-value">{{ $booking->user->name }} ({{ $booking->user->phone }})</div>
            </div>

            <div class="info-section">
                <div class="info-label">Tanggal Main</div>
                <div class="info-value">{{ \Carbon\Carbon::parse($booking->booking_date)->translatedFormat('l, d F Y') }}</div>
            </div>

            <div class="info-section" style="border-bottom: none;">
                <div class="info-label">Rincian Lapangan & Sesi Jam</div>
                @foreach($booking->bookingDetails as $detail)
                    <div class="slot-box">
                        <div style="font-weight: bold; color: #2d3748;">{{ $detail->field->name ?? 'Lapangan' }}</div>
                        <div style="font-size: 14px; color: #4a5568;">
                            Jam: {{ date('H:i', strtotime($detail->start_time)) }} - {{ date('H:i', strtotime($detail->end_time)) }} WIB
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="info-section" style="background: #f0fdf4; padding: 12px; border-radius: 12px; border: none; margin-top: 10px;">
                <div class="info-label" style="color: #166534;">Total Pembayaran (Lunas)</div>
                <div class="info-value" style="color: #166534; font-size: 18px; font-weight: 800;">
                    Rp {{ number_format($booking->total_amount, 0, ',', '.') }}
                </div>
            </div>
        </div>

        <div class="ticket-footer">
            <p>Penting: Tunjukkan email E-Ticket ini kepada petugas lapangan saat tiba di lokasi untuk validasi masuk.</p>
            <p>&copy; {{ date('Y') }} AyokMain. Semua Hak Dilindungi.</p>
        </div>
    </div>

</body>
</html>