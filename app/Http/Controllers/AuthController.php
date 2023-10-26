<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Models\Customer;
use App\Models\AkunCustomer;
use App\Models\AkunPegawai;
use App\Mail\PasswordResetMail;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function loginCustomer(Request $request)
    {
        $credentials = $request->only('email', 'password');
        // Validation rules for login
        $validate = Validator::make($credentials, [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if($validate->fails()){
            return response()->json([
                'message' => $validate->errors()
            ], 400);
        }


        if (Auth::guard('customer')->attempt($credentials)) {
            // Authentication passed
            $user = Auth::guard('customer')->user();
            $logCustomer = $user->customers;
            $role = $logCustomer->role;

            return response()->json([
                'message' => 'Login Customer successful',
                'data' => $user,
                'auth' => [
                    'token' => $user->createToken('authToken', [$role])->plainTextToken
                ]
            ], 200);
        }

        return response()->json(['message' => 'Invalid credentials'], 401);
    }
    
    public function loginPegawai(Request $request)
    {
        $credentials = $request->only('email', 'password');
        // Validation rules for login
        $validate = Validator::make($credentials, [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if($validate->fails()){
            return response()->json([
                'message' => $validate->errors()
            ], 400);
        }

        if (Auth::guard('pegawai')->attempt($credentials)) {
            $user = Auth::guard('pegawai')->user();
            $role = $user->role;
            
            return response()->json([
                'message' => 'Login Pegawai successful',
                'data' => $user,
                'auth' => [
                    'token' => $user->createToken('authToken', [$role])->plainTextToken
                ],
            ], 200);
            // return response()->json(['message' => 'Login Pegawai successful'], 200);
        }

        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    public function register(Request $request)
    {
        $credentials = $request->all();

 
        $validationRules = [
            'nama' => 'required|string',
            'email' => 'required|email|unique:customers,email|unique:akun_pegawais,email',
            'no_identitas' => 'required|string',
            'no_telepon' => 'required|numeric',
            'alamat' => 'required|string',
            'role' => [
                'required',
                'string',
                Rule::in(['G', 'P']),
            ],
        ];

        if ($request->input('role') === 'P') {
            $validationRules['password'] = 'required|string';
        }
    
        $validate = Validator::make($request->all(), $validationRules);

        if($validate->fails()){
            return response()->json([
                'message' => $validate->errors()
            ], 400);
        }

        $customer = Customer::create($credentials);

        if ($request->input('role') === 'P') {
            $akun = AkunCustomer::create([
                'id_customer' => $customer->id, 
                'email' => $customer->email,
                'password' => bcrypt($request->input('password')),
            ]);
        } else {
            $akun = null;
        }

        return response()->json([
            'message' => 'Registration successful. Please login.',
            'data' => [
                'customer' => $customer,
                'akun' => $akun,
            ],
        ], 200);
    }

    public function forgotPassword(Request $request)
    {
        $email = $request->input('email');
        $role = $request->input('role');

        if($role === 'C') {
            $user = Customer::where('email', $email)->first();
        }elseif ($role === 'P') {
            $user = AkunPegawai::where('email', $email)->first();
        }else {
            return response()->json(['message' => 'Role not found'], 404);
        }

        if (!$user) {
            return response()->json(['message' => 'User with this email not found'], 404);
        }

        $resetToken = mt_rand(1000, 9999);

        cache()->put('reset_token_' . $user->id, $resetToken, now()->addMinutes(5));

        \Mail::to($user->email)->send(new PasswordResetMail($resetToken));

        return response()->json(['message' => 'Password reset email sent']);
    }

    public function resetPassword(Request $request)
    {
        $email = $request->input('email');
        $role = $request->input('role');
        $resetToken = $request->input('token');
        $newPassword = $request->input('password');
    
        if($role === 'C') {
            $user = AkunCustomer::where('email', $email)->first();
        }elseif ($role === 'P') {
            $user = AkunPegawai::where('email', $email)->first();
        }else {
            return response()->json(['message' => 'Role not found'], 404);
        }
    
        if (!$user) {
            return response()->json(['message' => 'User with this email not found'], 404);
        }

        // echo 'Reset Token: ' . $resetToken;
        $cachedToken = cache()->get('reset_token_' . $user->id);
        // echo 'Cached Token: ' . $cachedToken;
    
        if (trim($cachedToken) !== trim($resetToken)) {
            return response()->json(['message' => 'Invalid reset token or email'], 400);
        }        

        $user->update(['password' => Hash::make($newPassword)]);

        cache()->forget('reset_token_' . $user->id);
    
        return response()->json(['message' => 'Password reset successfully']);
    }
    

    public function logout()
    {
        if (Auth::user()) {
            Auth::user()->tokens()->delete();
            return response()->json(['message' => 'You have been logged out'], 200);
        } else {
            return response()->json(['message' => 'No user is currently authenticated'], 401);
        }
    }

}
