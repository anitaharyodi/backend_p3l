<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reservasi;
use App\Models\Kamar;
use App\Models\Season;
use App\Models\TarifSeason;
use App\Models\JenisKamar;
use App\Models\ReservasiKamar;

class ReservasiKamarController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $tgl_checkin = $request->input('tgl_checkin');

        $allRooms = Kamar::with('jenisKamars', 'reservasiKamars.reservasis.customers')->get();

        $rooms = $allRooms->map(function ($room) use ($tgl_checkin) {
            $isAvailable = true;

            foreach ($room->reservasiKamars as $reservasiKamar) {
                $tglCheckin = $reservasiKamar->reservasis->tgl_checkin;
                $tglCheckout = $reservasiKamar->reservasis->tgl_checkout;

                if ($tgl_checkin >= $tglCheckin && $tgl_checkin <= $tglCheckout && $reservasiKamar->reservasis->status === 'Check-In') {
                    $isAvailable = false;
                    break;
                }
            }

            return [
                'room' => $room,
                'is_available' => $isAvailable,
            ];
        });

        return response()->json(['rooms' => $rooms]);
    }




    public function ketersediaanKamar(Request $request) {
        $check_in = $request->input('tgl_checkin');
        $check_out = $request->input('tgl_checkout');
    
        $jumlahKamarPerJenisKamar = Kamar::groupBy('id_jenis_kamar')
            ->select('id_jenis_kamar', \DB::raw('count(no_kamar) as totalKamar'))
            ->get();
    
        $jmlKamarSudahDipakai = Reservasi::where(function ($query) use ($check_in, $check_out) {
            $query->where('tgl_checkin', '<', $check_in)
                ->where('tgl_checkout', '>', $check_in);
        })->orWhere(function ($query) use ($check_in, $check_out) {
            $query->where('tgl_checkin', '<', $check_out)
                ->where('tgl_checkout', '>', $check_out);
        })->orWhere(function ($query) use ($check_in, $check_out) {
            $query->where('tgl_checkin', '>=', $check_in)
                ->where('tgl_checkout', '<=', $check_out);
        })->with('reservasiKamars')->get();
    
        if ($jmlKamarSudahDipakai !== null && $jmlKamarSudahDipakai->count() > 0) {
            foreach ($jmlKamarSudahDipakai as $reservasi) {
                foreach ($reservasi->reservasiKamars as $rooms) {
                    $idJK = $rooms->id_jenis_kamar;
                    $objJK = $jumlahKamarPerJenisKamar->first(function ($item) use ($idJK) {
                        return $item->id_jenis_kamar === $idJK;
                    });
    
                    if ($objJK && $objJK->totalKamar > 0) {
                        $objJK->totalKamar -= 1;
                    }
                }
            }
    
            return response()->json(['status' => 'F', 'message' => 'Sudah ada reservasi lain di tanggal tersebut!', 'data' => $jumlahKamarPerJenisKamar], 200);
        } else {
            return response()->json(['status' => 'T', 'message' => 'Belum ada reservasi', 'data' => $jumlahKamarPerJenisKamar], 200);
        }
    }    
    

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
