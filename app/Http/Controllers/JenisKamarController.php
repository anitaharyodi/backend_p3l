<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\JenisKamar;
use App\Models\Season;
use Illuminate\Validation\Rule;
use Validator;
use Carbon\Carbon;

class JenisKamarController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = JenisKamar::with(['tarifSeasons'])->get();

        return response()->json(['mess' => $data]);
    }

    /**
     * Create a new jenis kamar (create method).
     */
    public function create(Request $request)
    {
        $data = $request->all();
        $jenisKamar = JenisKamar::create($data);

        return response()->json(['message' => 'Jenis Kamar created successfully', 'data' => $jenisKamar], 201);
    }

    /**
     * Store a newly created jenis kamar in the database (store method).
     */
    public function store(Request $request)
    {
        $credentials = $request->all();
        $validate = Validator::make($credentials, [
            'jenis_kamar' => 'required|string',
            'kapasitas' => 'required|numeric',
            'tipe_bed' => 'required|string',
            'ukuran_kamar' => 'required|string',
            'rincian_kamar' => 'required|string',
            'deskripsi_kamar' => 'required|string',
            'tarif_normal' => 'required|numeric',
        ]);

        if($validate->fails()){
            return response()->json([
                'status' => 'F',
                'message' => $validate->errors()
            ], 400);
        }

        $jenisKamar = JenisKamar::create($credentials);

        return response()->json(['status' => 'T', 'message' => 'Jenis Kamar created successfully', 'data' => $jenisKamar], 201);
    }


    /**
     * Display a specific jenis kamar (show method).
     */
    public function show(string $id)
    {
        $jenisKamar = JenisKamar::with(['tarifSeasons'])->find($id);

        if ($jenisKamar) {
            return response()->json(['message' => 'Jenis Kamar details', 'data' => $jenisKamar]);
        } else {
            return response()->json(['message' => 'Jenis Kamar not found'], 404);
        }
    }

    /**
     * Update a specific jenis kamar (update method).
     */
    public function update(Request $request, string $id)
    {
        $jenisKamar = JenisKamar::find($id);

        if(!$jenisKamar){
            return response()->json(['status' => 'F', 'message' => 'Jenis Kamar not found'], 404);
        }

        $credentials = $request->all();
        $validate = Validator::make($credentials, [
            'jenis_kamar' => 'required|string',
            'kapasitas' => 'required|numeric',
            'tipe_bed' => 'required|string',
            'ukuran_kamar' => 'required|string',
            'rincian_kamar' => 'required|string',
            'deskripsi_kamar' => 'required|string',
            'tarif_normal' => 'required|numeric',
        ]);

        if($validate->fails()){
            return response()->json([
                'status' => 'F',
                'message' => $validate->errors()
            ], 400);
        }

        $jenisKamar->update($request->all());

        return response()->json(['status' => 'T', 'message' => 'Jenis Kamar updated successfully', 'data' => $jenisKamar]);
    }

    /**
     * Remove a specific jenis kamar from the database (destroy method).
     */
    public function destroy(string $id)
    {
        $jenisKamar = JenisKamar::find($id);

        if ($jenisKamar) {
            $jenisKamar->delete();
            return response()->json(['status' => 'T', 'message' => 'Jenis Kamar deleted successfully']);
        } else {
            return response()->json(['status' => 'F','message' => 'Jenis Kamar not found'], 404);
        }
    }

    public function tarifBySeason(Request $request) {
        $check_in = $request->input('tgl_checkin');
        $check_out = $request->input('tgl_checkout');
    
        $season = Season::where('tanggal_mulai', '<=', $check_in)
            ->where('tanggal_selesai', '>=', $check_in)
            ->first();
    
        $jenisSeason = $season ? $season->jenis_season : null;
        $tarifTotal = $season ? $season->tarif_total : 0;

        $jenisKamars = JenisKamar::with(['tarifSeasons'])->get();
    
        foreach ($jenisKamars as $jenisKamar) {
            $tarifNormal = $jenisKamar->tarif_normal;
            $tarifSeasons = $jenisKamar->tarifSeasons;
    
            if ($season) {
                foreach ($tarifSeasons as $tarifSeason) {
                    if ($tarifSeason->id_season == $season->id) {
                        if ($jenisSeason == 'Low') {
                            $tarifNormal -= $tarifSeason->tarif;
                        } elseif ($jenisSeason == 'High') {
                            $tarifNormal += $tarifSeason->tarif;
                        }
                    }
                }
            }
    
            $jenisKamar->tarifBySeason = $tarifNormal;
            unset($jenisKamar->tarifSeasons);
        }
    
        return response()->json(['message' => 'Success', 'data' => $jenisKamars], 200);
    }
    
    
}
