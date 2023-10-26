<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\FasilitasTambahan;
use Illuminate\Validation\Rule;
use Validator;
use Carbon\Carbon;

class FasilitasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = FasilitasTambahan::all();

        return response()->json(['mess' => $data]);
    }

    /**
     * Create a new fasilitas tambahan (create method).
     */
    public function create(Request $request)
    {
        $data = $request->all();
        $fasilitas = FasilitasTambahan::create($data);

        return response()->json(['message' => 'Fasilitas Tambahan created successfully', 'data' => $fasilitas], 201);
    }

    /**
     * Store a newly created fasilitas tambahan in the database (store method).
     */
    public function store(Request $request)
    {
        $credentials = $request->all();
        $validate = Validator::make($credentials, [
            'nama_fasilitas' => 'required|string',
            'harga' => 'required|numeric',
            'satuan' => 'required|string',
        ]);

        if($validate->fails()){
            return response()->json([
                'status' => 'F',
                'message' => $validate->errors()
            ], 400);
        }

        $fasilitas = FasilitasTambahan::create($credentials);

        return response()->json(['status' => 'T', 'message' => 'Fasilitas Tambahan created successfully', 'data' => $fasilitas], 201);
    }


    /**
     * Display a specific fasilitas tambahan (show method).
     */
    public function show(string $id)
    {
        $fasilitas = FasilitasTambahan::find($id);

        if ($fasilitas) {
            return response()->json(['message' => 'Fasilitas details', 'data' => $fasilitas]);
        } else {
            return response()->json(['message' => 'Fasilitas not found'], 404);
        }
    }

    /**
     * Update a specific fasilitas tambahan (update method).
     */
    public function update(Request $request, string $id)
    {
        $fasilitas = FasilitasTambahan::find($id);

        if(!$fasilitas){
            return response()->json(['status' => 'F', 'message' => 'Fasilitas Tambahan not found'], 404);
        }

        $credentials = $request->all();
        $validate = Validator::make($credentials, [
            'nama_fasilitas' => 'required|string',
            'harga' => 'required|numeric',
            'satuan' => 'required|string',
        ]);

        if($validate->fails()){
            return response()->json([
                'status' => 'F',
                'message' => $validate->errors()
            ], 400);
        }

        $fasilitas->update($request->all());

        return response()->json(['status' => 'T', 'message' => 'Fasilitas Tambahan updated successfully', 'data' => $fasilitas]);
        
    }

    /**
     * Remove a specific fasilitas tambahan from the database (destroy method).
     */
    public function destroy(string $id)
    {
        $fasilitas = FasilitasTambahan::find($id);

        if ($fasilitas) {
            $fasilitas->delete();
            return response()->json(['status' => 'T', 'message' => 'Fasilitas Tambahan deleted successfully']);
        } else {
            return response()->json(['status' => 'F','message' => 'Fasilitas Tambahan not found'], 404);
        }
    }
}
