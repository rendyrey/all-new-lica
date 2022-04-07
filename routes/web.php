<?php

use Illuminate\Support\Facades\Route;
use App\User;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect('login');
});

Auth::routes();

Route::get('home', 'HomeController@index')->name('home');
Route::get('check-email', function (Request $request) {
    $user = User::where('email', $request->email)->exists();
    return response()->json(['valid' => !$user]);
});
Route::get('check-username', function (Request $request) {
    $user = User::where('username', $request->username)->exists();
    return response()->json(['valid' => !$user]);
});

Route::get('send', 'MailController@send');


// Dashboard
Route::middleware(['auth'])->group(function () {
    Route::get('dashboard', 'DashboardController@index')->name('dashboard');

    Route::get('master/{masterData}', 'MasterController@index');
    Route::post('master/{masterData}/create', 'MasterController@create');
    Route::get('master/datatable/{masterData}', 'MasterController@datatable');
});
