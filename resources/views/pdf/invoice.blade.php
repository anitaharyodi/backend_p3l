<!DOCTYPE html>
<html>
<head>
    <title>Invoice</title>

    <style>
        h2 {
            font-size: 20px;
            text-align: center;
            border-top: 1px solid #000; 
            border-bottom: 1px solid #000; 
            padding: 10px 0; 
            margin-bottom: 0px;
        }
        .logo {
            text-align: center;
            padding: 10px 0; 
        }
        p {
            font-size: 18px;
        }
        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <div class="logo">
        <img src="{{ public_path('LOGOBAWAH.png') }}" width="200" alt="Your Logo">
    </div>
    <h2 style="text-align: center ">INVOICE</h2>
    <p style="text-align: right">Date: {{ $reservation->tgl_checkout }}</p>
    <p style="text-align: right">No. Invoice: {{ $reservation->invoices[0]->no_invoice }}</p>
    <p style="text-align: right">Front Office: {{ $reservation->frontOffices->nama }}</p>
    <p class="id-booking">ID Booking: {{ $reservation->id_booking }}</p>
    @if ($reservation->id_sm)
    <p style="margin-top: -3; margin-bottom: 30">PIC: {{ $reservation->salesMarketings->nama }}</p>
    @endif
    <p>Customer Name: {{ $reservation->customers->nama }}</p>
    <p>Address: {{ $reservation->customers->alamat }}</p>
    <h2 style="text-align: center ">DETAIL</h2>
    <p>Check-In: {{ $reservation->tgl_checkin }}</p>
    <p>Check-Out: {{ $reservation->tgl_checkout }}</p>
    <p>Adult: {{ $reservation->jumlah_dewasa }}</p>
    <p>Child: {{ $reservation->jumlah_anak }}</p>
    <p>Payment Date: {{ $reservation->tgl_pembayaran }}</p>
    <h2 style="text-align: center ">ROOM DETAIL</h2>

    @php
        $consolidatedData = [];
        foreach ($reservation->reservasiKamars as $reservasiKamar) {
            $key = $reservasiKamar->jenisKamars->jenis_kamar . ' ' . $reservasiKamar->jenisKamars->tipe_bed;
            if (isset($consolidatedData[$key])) {
                $consolidatedData[$key]['amount']++;
            } else {
                $consolidatedData[$key] = [
                    'room_type' => $reservasiKamar->jenisKamars->jenis_kamar,
                    'amount' => 1,
                    'price' => $reservasiKamar->hargaPerMalam,
                ];
            }
        }
    @endphp

    @php
    function calculateNights($checkin, $checkout) {
        $checkinDate = new DateTime($checkin);
        $checkoutDate = new DateTime($checkout);
        $interval = $checkinDate->diff($checkoutDate);
        return $interval->days;
    }
    @endphp

    <table style="width: 100%; margin-top: 20px;">
        <tr>
            <th>Night(s)</th>
            <th>Room Type</th>
            <th>Amount</th>
            <th>Price</th>
            <th>Total Price</th>
        </tr>
        @php
        $prevRoomType = '';
        $totalSum = 0;
        @endphp
        @foreach ($consolidatedData as $data)
        <tr>
            <td>{{ calculateNights($reservation->tgl_checkin, $reservation->tgl_checkout) }}</td>
            @if ($prevRoomType !== $data['room_type'])
            <td>{{ $data['room_type'] }}</td>
            @endif
            <td>{{ $data['amount'] }}</td>
            <td>Rp. {{ number_format($data['price'], 0, ',', '.') }}</td>
            <td>Rp. {{ number_format($data['amount'] * $data['price'] * intval(calculateNights($reservation->tgl_checkin, $reservation->tgl_checkout)), 0, ',', '.') }}</td>
            @php
            $prevRoomType = $data['room_type'];
            $totalSum += $data['amount'] * $data['price'] * intval(calculateNights($reservation->tgl_checkin, $reservation->tgl_checkout));
            @endphp
        </tr>
        @endforeach
        <tr>
            <td colspan="4" style="text-align: right; border: none"></td>
            <td><strong>Rp. {{ number_format($totalSum, 0, ',', '.') }}</strong></td>
        </tr>
    </table>

    @if ($reservation->transaksiFasilitas->isNotEmpty())
    <h2 style="text-align: center; margin-bottom: 0px">FACILITY DETAIL</h2>
    <table style="width: 100%;">
        <tr>
            <th>Name</th>
            <th>Date</th>
            <th>Amount</th>
            <th>Price</th>
            <th>Subtotal</th>
        </tr>
        @foreach($reservation->transaksiFasilitas as $reservasiFasilitas)
            <tr>
                <td>{{ $reservasiFasilitas->fasilitasTambahans->nama_fasilitas }}</td>
                <td>{{ $reservasiFasilitas->tgl_pemakaian }}</td>
                <td>{{ $reservasiFasilitas->jumlah }}</td>
                <td>Rp. {{ number_format($reservasiFasilitas->fasilitasTambahans->harga, 0, ',', '.') }}</td>
                <td>Rp. {{ number_format($reservasiFasilitas->subtotal, 0, ',', '.') }}</td>
            </tr>
            @endforeach
            <tr>
                <td colspan="4" style="text-align: right; border: none"></td>
                <td><strong>Rp. {{ number_format($reservation->invoices[0]->total_harga_layanan, 0, ',', '.') }}</strong></td>
            </tr>
    </table>
    @endif

    <p style="text-align: right">Tax: Rp. {{ number_format($reservation->invoices[0]->pajak_layanan, 0, ',', '.') }}</p>
    <p style="text-align: right; font-weight: bold">TOTAL: <strong>Rp. {{ number_format($reservation->invoices[0]->harga_total, 0, ',', '.') }}</strong></p>
    <div style="margin: 20px"></div>
    <p style="text-align: right">Down Payment: Rp. {{ number_format($reservation->uang_jaminan, 0, ',', '.') }}</p>
    <p style="text-align: right">Deposit: Rp. {{ number_format($reservation->deposit, 0, ',', '.') }}</p>

    @php
        $tempCash = $reservation->uang_jaminan + $reservation->deposit;
        $cash = $reservation->invoices[0]->harga_total - $tempCash;
        if ($cash < 0) {
            $cash = 0;
        }
    @endphp
    <p style="text-align: right; font-size: 20px; font-weight: bold">CASH: Rp. {{ number_format($cash, 0, ',', '.') }}</p>
    <p style="text-align: center">Thank You For Your Visit!</p>


</body>
</html>
