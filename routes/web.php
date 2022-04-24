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
    if ($user) {
        return response()->json('Email has already been taken');
    }
    return response()->json('true');
});
Route::get('check-username', function (Request $request) {
    $user = User::where('username', $request->username)->exists();
    if ($user) {
        return response()->json('Username has already been taken');
    }
    return response()->json('true');
});

Route::get('send', 'MailController@send');


// Dashboard
Route::middleware(['auth'])->group(function () {
    // BEGIN Pre Analytics
    Route::get('pre-analytics', 'PreAnalyticController@index')->name('pre-analytics');
    Route::get('pre-analytics/datatable', 'PreAnalyticController@datatable');
    Route::post('pre-analytics/test/{roomClass}/datatable', 'PreAnalyticController@datatableTest');
    Route::post('pre-analytics/test/{roomClass}/datatable/withoutId/{ids}', 'PreAnalyticController@datatableSelectTest');
    Route::post('pre-analytics/create', 'PreAnalyticController@create');
    // END Pre Analytics

    // BEGIN all route for master data
    Route::get('master/{masterData}', 'MasterController@index');
    Route::post('master/{masterData}/create', 'MasterController@create');
    Route::get('master/{masterData}/edit/{id}', 'MasterController@edit');
    Route::put('master/{masterData}/update', 'MasterController@update');
    Route::delete('master/{masterData}/delete/{id}', 'MasterController@delete');
    // datatable route for master data
    Route::get('master/datatable/{masterData}/{with?}', 'MasterController@datatable');
    Route::get('master/range/{testId}', 'MasterController@rangeDatatable');
    // END all route for master data
    Route::get('master/test-packages/{Ids}', 'MasterController@getTestPackage');

    // for select option form
    Route::get('master/select-options/{masterData}/{searchKey}', 'MasterController@selectOptions');
});
