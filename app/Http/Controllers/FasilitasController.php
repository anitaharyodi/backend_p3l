<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\FasilitasTambahan;
use App\Models\Reservasi;
use App\Models\TransaksiFasilitas;
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

    public function transaksiFasilitas(Request $request, string $id) {
        $reservasi = Reservasi::find($id);
    
        if (!$reservasi) {
            return response()->json([
                'status' => 'F',
                'message' => 'Reservasi not found'
            ], 404);
        }
    
        $data = $request->json()->all();
    
        $transaksiFasilitasList = [];
    
        foreach ($data as $item) {
            $credentials = [
                'id_fasilitas' => $item['id_fasilitas'],
                'tgl_pemakaian' => $item['tgl_pemakaian'],
                'jumlah' => $item['jumlah'],
                'subtotal' => $item['subtotal'],
                'id_reservasi' => $reservasi->id,
            ];
    
            $validate = Validator::make($credentials, [
                'id_fasilitas' => 'required',
                'tgl_pemakaian' => 'required|date',
                'jumlah' => 'required|numeric',
                'subtotal' => 'required',
            ]);
    
            if ($validate->fails()) {
                return response()->json([
                    'status' => 'F',
                    'message' => $validate->errors()
                ], 400);
            }
    
            $transaksiFasilitas = TransaksiFasilitas::create($credentials);
            $transaksiFasilitas->load('fasilitasTambahans'); 
            $transaksiFasilitasList[] = $transaksiFasilitas;
        }
    
        return response()->json([
            'status' => 'T',
            'message' => 'Transaksi Fasilitas created successfully',
            'data' => $transaksiFasilitasList,
        ], 200);
    }
}
