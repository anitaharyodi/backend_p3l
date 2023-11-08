<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reservasi;
use App\Models\ReservasiKamar;
use Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use PDF;


class ReservasiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data = $request->all();
        $reservasi = Reservasi::create($data);

        return response()->json(['message' => 'Reservasi created successfully', 'data' => $reservasi], 201);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
    $credentials = $request->all();
    
    // Validate the input fields
    $validate = Validator::make($credentials, [
        'tgl_checkin' => 'required|date',
        'tgl_checkout' => 'required|date|after:tgl_checkin',
        'jumlah_dewasa' => 'required|numeric',
        'jumlah_anak' => 'required|numeric',
        'jenis_kamar' => 'required',
    ]);

    if ($validate->fails()) {
        return response()->json([
            'status' => 'F',
            'message' => $validate->errors()
        ], 400);
    }

    $user = Auth::user();
    $id = $user->id_customer;
    $idPegawai = $user->id;

    $prefix = ($id) ? 'P' : 'G';

    // Generate the booking ID
    $date = now();
    $formattedDate = $date->format('dmy');
    $nomorLastBookingUntukHariIni = Reservasi::where('id_booking', 'LIKE', $prefix . $formattedDate . '-%')
    ->orderBy('id', 'desc')
    ->limit(1)
    ->value(\DB::raw('RIGHT(id_booking, 3)'));
    
    $increment = '001';
    if ($nomorLastBookingUntukHariIni) {
        $increment = str_pad($nomorLastBookingUntukHariIni + 1, 3, '0', STR_PAD_LEFT);
    }
    $bookingId = $prefix . $formattedDate . '-' . $increment;

   
    $reservasiData = array_merge($credentials, ['id_booking' => $bookingId, 'tgl_reservasi' => $date, 'status' => 'Waiting for payment']);

    if ($id !== null) {
        $reservasiData['id_customer'] = $id;
    }else {
        $reservasiData['id_sm'] = $idPegawai;
    }


    $total = 0;
    foreach ($credentials['jenis_kamar'] as $jenisKamar) {
        $jumlah = $jenisKamar['jumlah'];
        for ($i = 0; $i < $jumlah; $i++) {
            $total += $jumlah;
        }
    }

    $totalOrang = $credentials['jumlah_dewasa'] + $credentials['jumlah_anak'];
    $minimalKamarPesan = floor($totalOrang/2);
    if($total < $minimalKamarPesan) {
        return response()->json([
            'status' => 'F',
            'message' => "Minimum number of rooms booked must be {$minimalKamarPesan}! "
        ], 400);
    }

    $reservasi = Reservasi::create($reservasiData);

    $reservasiKamars = [];

    // // Process the jenis_kamar data
    // $jenisKamarData = $request->input('jenis_kamar');

    if (is_array($credentials['jenis_kamar'])) {
        foreach ($credentials['jenis_kamar'] as $jenisKamar) {
            $id_jenis_kamar = $jenisKamar['id_jenis_kamar'];
            $jumlah = $jenisKamar['jumlah'];
            $hargaPerMalam = $jenisKamar['hargaPerMalam'];

            for ($i = 0; $i < $jumlah; $i++) {
                $reservasiKamar = ReservasiKamar::create([
                    'id_reservasi' => $reservasi->id,
                    'id_jenis_kamar' => $id_jenis_kamar,
                    'hargaPerMalam' => $hargaPerMalam,
                ]);
                $reservasiKamars[] = $reservasiKamar;
            }
        }
    }


    return response()->json(['status' => 'T', 'message' => 'Reservasi created successfully', 'data' => ['reservasi' => $reservasi, 'reservasiKamar' => $reservasiKamars]], 201);

    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $detailHistory = Reservasi::with(['customers', 'salesMarketings', 'frontOffices', 'transaksiFasilitas.fasilitasTambahans', 'reservasiKamars.jenisKamars'])->find($id);

        if ($detailHistory) {
            return response()->json(['message' => 'History details', 'data' => $detailHistory]);
        } else {
            return response()->json(['message' => 'History details not found'], 404);
        }
    }

    public function uploadPembayaran(Request $request, $id)
    {
        $reservasi = Reservasi::find($id);

        if (!$reservasi) {
            return response()->json(['status' => 'F', 'message' => 'Reservation not found'], 404);
        }

        if ($request->hasFile('bukti_transfer')) {
            $file = $request->file('bukti_transfer');

            $allowedTypes = ['jpg', 'jpeg', 'png'];
            $fileExtension = $file->getClientOriginalExtension();

            if (!in_array(strtolower($fileExtension), $allowedTypes)) {
                return response()->json(['status' => 'F', 'message' => 'Invalid file format'], 400);
            }

            $filename = time() . '_' . $file->getClientOriginalName();

            $file->storeAs('public/bukti_transfer', $filename);

            $reservasi->bukti_transfer = 'bukti_transfer/' . $filename;

            $reservasi->tgl_pembayaran = now();

            if ($reservasi->id_sm) {
                // If id_sm is present, request uang_jaminan input
                if (!$request->has('uang_jaminan')) {
                    return response()->json(['status' => 'F', 'message' => 'Please provide uang_jaminan'], 400);
                }
                $uangJaminan = (float) $request->input('uang_jaminan');
                $totalHarga = (float) $reservasi->total_harga;

                if ($uangJaminan < 0.5 * $totalHarga) {
                    return response()->json(['status' => 'F', 'message' => 'Uang jaminan must be at least 50% of total harga'], 400);
                }
                $reservasi->uang_jaminan = $uangJaminan;
            }else {
                $reservasi->uang_jaminan = $reservasi->total_harga;
            }

            $reservasi->status = 'Confirmed';

            $reservasi->save();

            $fileUrl = asset('storage/' . $reservasi->bukti_transfer);

            return response()->json(['status' => 'T', 'message' => 'Payment proof uploaded successfully', 'file_url' => $fileUrl, $reservasi], 200);
        }

        return response()->json(['status' => 'F', 'message' => 'No file provided for upload'], 400);
    }
    
    public function pemesananBatal(string $id) {
        $reservation = Reservasi::find($id);

        if (!$reservation) {
            return response()->json(['status' => 'F', 'message' => 'Reservation not found'], 404);
        }

        $oneWeekBeforeCheckin = Carbon::parse($reservation->tgl_checkin)->subWeek();

        if ($reservation->status === 'Confirmed' && Carbon::now()->lessThanOrEqualTo($oneWeekBeforeCheckin)) {
            $reservation->status = 'Cancelled';
            $reservation->uang_jaminan = null;
            $reservation->save();

            $reservation->reservasiKamars()->delete();

            return response()->json(['status' => 'T', 'message' => $reservation], 200);
        } else {
            $reservation->status = 'Cancelled';
            $reservation->save();

            $reservation->reservasiKamars()->delete();

            return response()->json(['status' => 'T', 'message' => $reservation], 200);
        }
    }

    public function generateReservationPDF($id)
    {
        $reservation = Reservasi::with(['customers', 'salesMarketings', 'frontOffices', 'transaksiFasilitas.fasilitasTambahans', 'reservasiKamars.jenisKamars'])->find($id);
    
        if (!$reservation) {
            return view('pdf.reservation-not-found');
        }


        $pdf = PDF::loadView('pdf.reservation', ['reservation' => $reservation]);
        $pdf->setPaper('A4', 'portrait');
        
        $bookingId = $reservation->id_booking;
        $filename = "reservation_$bookingId.pdf";
        
        return $pdf->download($filename);
    }
    


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $reservasi = Reservasi::find($id);

        if(!$reservasi){
            return response()->json(['status' => 'F', 'message' => 'Reservasi not found'], 404);
        }

        $credentials = $request->all();

        $reservasi->update($request->all());

        return response()->json(['status' => 'T', 'message' => 'Reservasi updated successfully', 'data' => $reservasi]);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
