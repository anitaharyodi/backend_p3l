<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use Validator;
use Illuminate\Validation\Rule;
use App\Models\AkunCustomer;
use App\Models\AkunPegawai;
use App\Models\ReservasiKamar;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;


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


    //Laporan 1
    public function customersPerMonth()
    {
        $months = [
            'January', 'February', 'March', 'April', 'May', 'June',
            'July', 'August', 'September', 'October', 'November', 'December'
        ];

        $currentYear = date('Y');

        $customersPerMonth = collect($months)->map(function ($month, $index) use ($currentYear) {
            $count = Customer::whereYear('created_at', $currentYear)
                ->whereMonth('created_at', $index + 1)
                ->count();

            return ['month' => $month, 'total' => $count];
        });

        return response()->json(['customers_per_month' => $customersPerMonth]);
    }

    // Laporan 4
    public function topCustomersWithMostReservations()
    {
        $currentYear = Carbon::now()->year;

        $topCustomers = Customer::withCount(['reservations as total_reservations' => function ($query) use ($currentYear) {
                $query->select(\DB::raw('COUNT(*)'))
                    ->whereYear('tgl_reservasi', $currentYear)
                    ->groupBy('id_customer')
                    ->orderByDesc('total_reservations')
                    ->limit(5);
            }])
            ->with(['reservations' => function ($query) use ($currentYear) {
                $query->select('id_customer', \DB::raw('COUNT(*) as total_reservations'), \DB::raw('SUM(total_harga) as total_payment'))
                    ->whereYear('tgl_reservasi', $currentYear)
                    ->groupBy('id_customer')
                    ->orderByDesc('total_reservations')
                    ->limit(5);
            }])
            ->orderByDesc('total_reservations')
            ->limit(5)
            ->get(['id', 'nama', 'total_reservations']);

        return response()->json(['top_customers' => $topCustomers]);
    }

}
