<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kamar;
use Illuminate\Validation\Rule;
use Validator;

class KamarController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Kamar::with(['jenisKamars'])->get();

        return response()->json(['mess' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data = $request->all();
        $kamar = Kamar::create($data);

        return response()->json(['message' => 'Kamar created successfully', 'data' => $kamar], 201);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $credentials = $request->all();
        $validate = Validator::make($credentials, [
            'no_kamar' => 'required|numeric|unique:kamars',
            'id_jenis_kamar' => 'required|numeric',
            'tipe_bed' => 'required|string',
        ]);

        if($validate->fails()){
            return response()->json([
                'status' => 'F',
                'message' => $validate->errors()
            ], 400);
        }

        $kamar = Kamar::create($credentials);

        return response()->json(['status' => 'T', 'message' => 'Kamar created successfully', 'data' => $kamar], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $kamar = Kamar::with(['jenisKamars'])->find($id);

        if ($kamar) {
            return response()->json(['message' => 'Kamar details', 'data' => $kamar]);
        } else {
            return response()->json(['message' => 'Kamar not found'], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $kamar = Kamar::find($id);

        if(!$kamar){
            return response()->json(['status' => 'F', 'message' => 'Kamar not found'], 404);
        }

        $credentials = $request->all();
        $validate = Validator::make($credentials, [
            'no_kamar' => 'required|numeric|unique:kamars,no_kamar,' . $id,
            'id_jenis_kamar' => 'required|numeric',
            'tipe_bed' => 'required|string',
        ]);

        if($validate->fails()){
            return response()->json([
                'status' => 'F',
                'message' => $validate->errors()
            ], 400);
        }

        $kamar->update($request->all());

        return response()->json(['status' => 'T', 'message' => 'Kamar updated successfully', 'data' => $kamar]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $kamar = Kamar::find($id);

        if ($kamar) {
            $kamar->delete();
            return response()->json(['status' => 'T', 'message' => 'Kamar deleted successfully']);
        } else {
            return response()->json(['status' => 'F','message' => 'Kamar not found'], 404);
        }
    }
}
