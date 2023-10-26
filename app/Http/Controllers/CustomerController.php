<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use Validator;
use Illuminate\Validation\Rule;
use App\Models\AkunCustomer;
use App\Models\AkunPegawai;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Customer::with('reservations')->where('role', 'G')->get();

        return response()->json(['mess' => $data]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $customer = Customer::with('reservations.salesMarketings', 'reservations.transaksiFasilitas.fasilitasTambahans', 'reservations.reservasiKamars.jenisKamars')->find($id);

        if ($customer) {
            return response()->json(['message' => 'Customer details', 'data' => $customer]);
        } else {
            return response()->json(['message' => 'Customer not found'], 404);
        }
    }

    public function getProfile()
    {
        $user = Auth::user();
        $logCustomer = $user->customers;
        
        return response()->json([
            'message' => 'Profile details',
            'data' => $user
        ], 200);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        $id = $user->id_customer;
        $customer = Customer::find($id);

        $credentials = $request->all();

        $validationRules = [
            'nama' => 'required|string',
            'email' => [
                'required',
                'email',
                Rule::unique('customers', 'email')->ignore($id),
                Rule::unique('akun_pegawais', 'email'),
            ],
            'no_identitas' => 'required|string',
            'no_telepon' => 'required|numeric',
            'alamat' => 'required|string'
        ];

        $validate = Validator::make($credentials, $validationRules);

        if($validate->fails()){
            return response()->json([
                'message' => $validate->errors()
            ], 400);
        }

        $customer->update($credentials);

        AkunCustomer::where('id_customer', $customer->id)->update(['email' => $request->input('email')]);

        return response()->json(['status' => 'T', 'message' => 'Customer updated successfully', 'data' => $customer]);
    }

    public function changePassword(Request $request)
    {
        $user = Auth::user();
    
        $credentials = $request->all();
    
        $validationRules = [
            'old_password' => 'required|string',
            'password' => 'required|string',
        ];
    
        $validate = Validator::make($credentials, $validationRules);
    
        if ($validate->fails()) {
            return response()->json([
                'message' => $validate->errors()
            ], 400);
        }
  
        if (!Hash::check($request->input('old_password'), $user->password)) {
            return response()->json(['status' => 'F', 'message' => 'Old password is incorrect'], 400);
        }

        $user->update(['password' => bcrypt($request->input('password'))]);
    
        return response()->json(['status' => 'T', 'message' => 'Password updated successfully']);
    }

    public function getAllHistoryCustomer() {
        $user = Auth::user();

        $tempCustomer = $user->customers;
        $tempReservation = $tempCustomer->reservations;

        return response()->json(['mess' => $user]);

    }

    public function getAllHistorySM() {
        $user = Auth::user();

        $customer = AkunPegawai::with('reservationsForSM.customers')->find($user->id);

        return response()->json(['mess' => $customer]);
    }

}
