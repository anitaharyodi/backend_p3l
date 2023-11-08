<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\TarifSeason;
use Illuminate\Validation\Rule;
use Validator;
use Carbon\Carbon;

class TarifSeasonController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = TarifSeason::with(['seasons', 'jenisKamars'])->get();

        return response()->json(['mess' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data = $request->all();
        $tarifSeason = TarifSeason::create($data);

        return response()->json(['message' => 'Tarif Season created successfully', 'data' => $tarifSeason], 201);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $credentials = $request->all();
        $validate = Validator::make($credentials, [
            'id_season' => 'required',
            'tarif' => 'required|string',
        ]);

        if($validate->fails()){
            return response()->json([
                'status' => 'F',
                'message' => $validate->errors()
            ], 400);
        }

        $checkTarif = TarifSeason::where('id_jenis_kamar', $request->id_jenis_kamar)->where('id_season', $request->id_season)->first();

        if ($checkTarif) {
        return response()->json([
            'status' => "F",
            'message' => 'Room type must be unique'
        ],400);}

        $tarifSeason = TarifSeason::create($credentials);

        return response()->json(['status' => 'T', 'message' => 'Tarif Season created successfully', 'data' => $tarifSeason], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $tarifSeason = TarifSeason::with(['seasons', 'jenisKamars'])->find($id);

        if ($tarifSeason) {
            return response()->json(['message' => 'Tarif Season details', 'data' => $tarifSeason]);
        } else {
            return response()->json(['message' => 'Tarif Season not found'], 404);
        }
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $tarifSeason = TarifSeason::find($id);

        if(!$tarifSeason){
            return response()->json(['status' => 'F', 'message' => 'Tarif Season not found'], 404);
        }

        $credentials = $request->all();
        $validate = Validator::make($credentials, [
            'id_season' => 'required',
            'tarif' => 'required|string',
        ]);

        if($validate->fails()){
            return response()->json([
                'status' => 'F',
                'message' => $validate->errors()
            ], 400);
        }

        $tarifSeason->update($request->all());

        return response()->json(['status' => 'T', 'message' => 'Tarif Season updated successfully', 'data' => $tarifSeason]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $tarifSeason = TarifSeason::find($id);

        if ($tarifSeason) {
            $tarifSeason->delete();
            return response()->json(['status' => 'T', 'message' => 'Tarif Season deleted successfully']);
        } else {
            return response()->json(['status' => 'F','message' => 'Tarif Season not found'], 404);
        }
    }
}
