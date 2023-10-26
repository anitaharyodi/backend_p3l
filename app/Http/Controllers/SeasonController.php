<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Season;
use Illuminate\Validation\Rule;
use Validator;
use Carbon\Carbon;

class SeasonController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Season::with(['tarifSeasons.jenisKamars'])->get();

        return response()->json(['mess' => $data]);
    }

    /**
     * Create a new season (create method).
     */
    public function create(Request $request)
    {
        $data = $request->all();
        $season = Season::create($data);

        return response()->json(['message' => 'Season created successfully', 'data' => $season], 201);
    }

    /**
     * Store a newly created season in the database (store method).
     */
    public function store(Request $request)
    {
        $credentials = $request->all();
        $validate = Validator::make($credentials, [
            'nama_season' => 'required|string',
            'jenis_season' => 'required|string',
            'tanggal_mulai' => [
                'required',
                'date',
                'after:' . now()->addMonths(2)->toDateString(),
                Rule::unique('seasons')->where(function ($query) {
                    return $query->where('tanggal_selesai', '>=', now()->addMonths(2)->toDateString());
                }),
            ],
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
        ], [
            'tanggal_mulai.after' => 'The start date must be exactly 2 months from now.',
            'tanggal_selesai.after' => 'The end date cannot be less than the start date.'
        ]);

        if($validate->fails()){
            return response()->json([
                'status' => 'F',
                'message' => $validate->errors()
            ], 400);
        }

        $season = Season::create($credentials);

        return response()->json(['status' => 'T', 'message' => 'Season created successfully', 'data' => $season], 201);
    }


    /**
     * Display a specific season (show method).
     */
    public function show(string $id)
    {
        $season = Season::with(['tarifSeasons'])->find($id);

        if ($season) {
            return response()->json(['message' => 'Season details', 'data' => $season]);
        } else {
            return response()->json(['message' => 'Season not found'], 404);
        }
    }

    /**
     * Update a specific season (update method).
     */
    public function update(Request $request, string $id)
    {

    $season = Season::find($id);

    if(!$season){
        return response()->json(['status' => 'F', 'message' => 'Season not found'], 404);
    }

    $credentials = $request->all();
    $validate = Validator::make($credentials, [
        'nama_season' => 'required|string',
        'jenis_season' => 'required|string',
        'tanggal_mulai' => [
            'required',
            'date',
            'after:' . now()->addMonths(2)->toDateString(),
        ],
        'tanggal_selesai' => 'required|date|after:tanggal_mulai',
    ], [
        'tanggal_mulai.after' => 'The start date must be exactly 2 months from now.',
        'tanggal_selesai.after' => 'The end date cannot be less than the start date.'
    ]);

    if ($validate->fails()) {
        return response()->json([
            'status' => 'F',
            'message' => $validate->errors()
        ], 400);
    }

    $season->fill($request->all())->save();

    return response()->json(['status' => 'T', 'message' => 'Season updated successfully', 'data' => $season]);
}


    /**
     * Remove a specific season from the database (destroy method).
     */
    public function destroy(string $id)
    {
        $season = Season::find($id);

        if ($season) {
            $twoMonthsFromNow = now()->addMonths(2);
            $startDate = \Carbon\Carbon::parse($season->tanggal_mulai);
    
            if ($startDate->gte($twoMonthsFromNow)) {
                $season->delete();
                return response()->json(['status' => 'T', 'message' => 'Season deleted successfully']);
            } else {
                return response()->json(['status' => 'F', 'message' => 'Season cannot be deleted as its within 2 months of the start date'], 400);
            }
        } else {
            return response()->json(['status' => 'F','message' => 'Season not found'], 404);
        }
    }
}
