<!DOCTYPE html>
<html>
<head>
    <title>Reservation Details</title>

    <style>
        /* Center the heading and add lines */
        h2 {
            font-size: 16;
            text-align: center;
            border-top: 1px solid #000; /* Top line */
            border-bottom: 1px solid #000; /* Bottom line */
            padding: 10px 0; /* Add some space around the heading */
        }
        .logo {
            text-align: center;
            padding: 10px 0; /* Adjust as needed */
        }
        .id-booking {
            display: inline-block;
        }

        /* Style the paragraph for "Tanggal Reservasi" and align it to the right */
        .tgl-reservasi {
            float: right;
        }
        p{
            font-size: 14;
        }
        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }

        /* Style table header cells (th) */
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <div class="logo">
        <img src="{{ public_path('LOGOBAWAH.png') }}" width="200" alt="Your Logo">
    </div>
    <h2 style="text-align: center ">RESERVATION RECEIPT</h2>
    <p class="id-booking">ID Booking: {{ $reservation->id_booking }}</p>
    <p class="tgl-reservasi">Reservation Date: {{ $reservation->tgl_reservasi }}</p>
    @if (!auth()->user()->id_customer)
    <p style="margin-top: -3; margin-bottom: 30">PIC: {{ auth()->user()->nama }}</p>
    @endif
    <p>Customer Name: {{ $reservation->customers->nama }}</p>
    <p>Address: {{ $reservation->customers->alamat }}</p>
    <h2 style="text-align: center ">RESERVATION DETAIL</h2>
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

    <table style="width: 100%">
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


    @if (!auth()->user()->id_customer)
    <p style="float: right">Down Payment: <strong>Rp. {{ number_format($reservation->uang_jaminan, 0, ',', '.') }}</strong></p>
    @endif
    <p>Special Request:</p>
    
    <p style="margin:0">{!! nl2br(e($reservation->special_req ?? '')) !!}</p>
    @foreach($reservation->transaksiFasilitas as $reservasiFasilitas)
        <p style="margin: 0">- ({{ $reservasiFasilitas->jumlah }}x) {{ $reservasiFasilitas->fasilitasTambahans->nama_fasilitas }}</p>
    @endforeach

</body>
</html>
