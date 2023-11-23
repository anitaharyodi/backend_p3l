<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::post('login', 'App\Http\Controllers\AuthController@loginCustomer');
Route::post('loginPegawai', 'App\Http\Controllers\AuthController@loginPegawai');
Route::post('register', 'App\Http\Controllers\AuthController@register');
Route::post('password/reset/request', 'App\Http\Controllers\AuthController@forgotPassword');
Route::post('password/reset', 'App\Http\Controllers\AuthController@resetPassword');
Route::get('jenisKamar', 'App\Http\Controllers\JenisKamarController@index');
Route::get('jenisKamar/{id}', 'App\Http\Controllers\JenisKamarController@show');
Route::get('ketersediaanKamar', 'App\Http\Controllers\ReservasiKamarController@ketersediaanKamar');
Route::get('tarifBySeason', 'App\Http\Controllers\JenisKamarController@tarifBySeason'); 



Route::middleware(['auth:sanctum', 'ability:Admin'])->group(function(){

    // Jenis Kamar
    Route::post('jenisKamar', 'App\Http\Controllers\JenisKamarController@store');
    Route::post('jenisKamar/{id}', 'App\Http\Controllers\JenisKamarController@update');
    Route::delete('jenisKamar/{id}', 'App\Http\Controllers\JenisKamarController@destroy');

    //Kamar
    Route::get('kamar', 'App\Http\Controllers\KamarController@index');
    Route::post('kamar', 'App\Http\Controllers\KamarController@store');
    Route::get('kamar/{id}', 'App\Http\Controllers\KamarController@show');
    Route::post('kamar/{id}', 'App\Http\Controllers\KamarController@update');
    Route::delete('kamar/{id}', 'App\Http\Controllers\KamarController@destroy');

});

Route::middleware(['auth:sanctum', 'ability:General Manager,Owner'])->group(function(){

    //Report
    Route::get('customersPerMonth', 'App\Http\Controllers\CustomerController@customersPerMonth');
    Route::get('totalPendapatan', 'App\Http\Controllers\ReservasiController@sumTotalHargaByPrefix');
    Route::get('customerJenisKamar/{month}', 'App\Http\Controllers\CustomerController@countCustomersInMonthByJenisKamar');
    Route::get('topCustomer', 'App\Http\Controllers\CustomerController@topCustomersWithMostReservations');
});

// Route::middleware(['auth:sanctum', 'ability:Owner'])->group(function(){

// });

Route::middleware(['auth:sanctum', 'ability:Sales Marketing'])->group(function(){

    // Season
    Route::get('season', 'App\Http\Controllers\SeasonController@index');
    Route::post('season', 'App\Http\Controllers\SeasonController@store');
    Route::get('season/{id}', 'App\Http\Controllers\SeasonController@show');
    Route::post('season/{id}', 'App\Http\Controllers\SeasonController@update');
    Route::delete('season/{id}', 'App\Http\Controllers\SeasonController@destroy');

    // Fasilitas Tambahan
    Route::post('fasilitas', 'App\Http\Controllers\FasilitasController@store');
    Route::get('fasilitas/{id}', 'App\Http\Controllers\FasilitasController@show');
    Route::post('fasilitas/{id}', 'App\Http\Controllers\FasilitasController@update');
    Route::delete('fasilitas/{id}', 'App\Http\Controllers\FasilitasController@destroy');

    // Tarif Season
    Route::get('tarifSeason', 'App\Http\Controllers\TarifSeasonController@index');
    Route::post('tarifSeason', 'App\Http\Controllers\TarifSeasonController@store');
    Route::get('tarifSeason/{id}', 'App\Http\Controllers\TarifSeasonController@show');
    Route::post('tarifSeason/{id}', 'App\Http\Controllers\TarifSeasonController@update');
    Route::delete('tarifSeason/{id}', 'App\Http\Controllers\TarifSeasonController@destroy');

    // Reservasi
    Route::get('historySM', 'App\Http\Controllers\CustomerController@getAllHistorySM');

});

Route::middleware(['auth:sanctum', 'ability:Front Office'])->group(function(){
    
    //Check In
    Route::get('dataReservasi', 'App\Http\Controllers\ReservasiController@index');
    Route::post('checkin/{id}', 'App\Http\Controllers\ReservasiController@checkIn');
    Route::get('/invoice-pdf/{id}', 'App\Http\Controllers\ReservasiController@generateNotaLunasPDF');
    Route::post('checkout/{id}', 'App\Http\Controllers\ReservasiController@checkOut');

    Route::get('kamar', 'App\Http\Controllers\KamarController@index');
    Route::get('reservasiKamar', 'App\Http\Controllers\ReservasiKamarController@index');
    

});

Route::middleware(['auth:sanctum', 'ability:P,Sales Marketing'])->group(function(){

    // Customer
    Route::get('customer', 'App\Http\Controllers\CustomerController@index');
    Route::get('customer/{id}', 'App\Http\Controllers\CustomerController@show');
    Route::get('getProfile', 'App\Http\Controllers\CustomerController@getProfile');

    //Reservasi
    Route::get('history/{id}', 'App\Http\Controllers\ReservasiController@show');
    Route::post('reservasi', 'App\Http\Controllers\ReservasiController@store');
    Route::post('/reservasi/upload-pembayaran/{id}', 'App\Http\Controllers\ReservasiController@uploadPembayaran');
    Route::post('/reservasi/pemesananBatal/{id}', 'App\Http\Controllers\ReservasiController@pemesananBatal');
    Route::get('/generate-pdf/{id}', 'App\Http\Controllers\ReservasiController@generateReservationPDF');
    Route::post('/reservasi/{id}', 'App\Http\Controllers\ReservasiController@update');


});

Route::middleware(['auth:sanctum', 'ability:P,Sales Marketing,Front Office'])->group(function(){

    //Fasilitas
    Route::get('fasilitas', 'App\Http\Controllers\FasilitasController@index');
    Route::post('transaksiFasilitas/{id}', 'App\Http\Controllers\FasilitasController@transaksiFasilitas');

});

Route::middleware(['auth:sanctum', 'ability:P'])->group(function(){

    // Customer
    Route::post('customer/updateProfile', 'App\Http\Controllers\CustomerController@update');
    Route::post('customer/changePassword', 'App\Http\Controllers\CustomerController@changePassword');
    Route::get('history', 'App\Http\Controllers\CustomerController@getAllHistoryCustomer');

    //Reservasi
    

});

Route::middleware(['auth:sanctum', 'ability:Sales Marketing,Front Office,Admin,P,General Manager,Owner'])->group(function(){
   
    // Logout
    Route::post('logout', 'App\Http\Controllers\AuthController@logout');
});