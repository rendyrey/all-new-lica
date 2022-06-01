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
    // badge info
    Route::get('main-layout/badge-info','Controller@badgeInfo');
    // end of badge info
    // BEGIN Pre Analytics
    Route::prefix('pre-analytics')->group(function(){
        Route::get('/', 'PreAnalyticController@index')->name('pre-analytics');
        // Analytics datatables
        Route::get('datatable/{startDate?}/{endDate?}', 'PreAnalyticController@datatable');
        Route::get('transaction-test/{transactionId}/datatable','PreAnalyticController@datatableTransactionTest');
        Route::get('transaction-specimen/{transactionId}/datatable','PreAnalyticController@datatableTransactionSpecimen');
        Route::post('edit-test/{roomClass}/{transactionId}/datatable', 'PreAnalyticController@datatableEditTest');
        Route::post('edit-test/selected-test/{roomClass}/{transactionId}', 'PreAnalyticController@selectedEditTest');
        Route::post('edit-test/add','PreAnalyticController@editTestAdd');
        Route::post('edit-test/update', 'PreAnalyticController@editTestUpdate');
        Route::post('transaction/note/update', 'PreAnalyticController@updateNote');
        Route::delete('edit-test/{transactionTestId}/delete','PreAnalyticController@editTestDelete');
        
        Route::post('test/{roomClass}/datatable/withoutId/{ids}', 'PreAnalyticController@datatableSelectTest');
        Route::post('test/{roomClass}/datatable', 'PreAnalyticController@datatableTest');
        // END of analytics datatable
        Route::get('analyzer-test/{testId}', 'PreAnalyticController@analyzerTest');
        Route::post('create', 'PreAnalyticController@create');
        Route::post('transaction-test/update-analyzer/{transactionTestId}', 'PreAnalyticController@updateAnalyzer');
        Route::post('specimen-test/update-draw', 'PreAnalyticController@updateDraw');
        Route::post('specimen-test/draw-all/{value}', 'PreAnalyticController@drawAll');
        Route::get('specimen-test/is-all-drawn/{transactionId}', 'PreAnalyticController@isAllDrawn');
        Route::post('check-in/{isManual?}', 'PreAnalyticController@checkIn');
        Route::delete('transaction/delete/{id}', 'PreAnalyticController@deleteTransaction');

        Route::get('check-medical-record/{medrec}', 'PreAnalyticController@checkMedRec');
        Route::get('edit-patient-details/{transactionId}', 'PreAnalyticController@editPatientDetails');
        Route::put('update-patient-details', 'PreAnalyticController@updatePatientDetails');

        Route::get('go-to-analytics-btn/{transactionId}', 'PreAnalyticController@goToAnalyticBtn');
        Route::put('go-to-analytics','PreAnalyticController@goToAnalytic');
    });
    // END Pre Analytics

    // BEGIN Analytics
    Route::prefix('analytics')->group(function(){
        Route::get('/', 'AnalyticController@index')->name('analytics');

        // Datatable
        Route::get('datatable/{startDate?}/{endDate?}', 'AnalyticController@datatable');
        Route::get('datatable-test/{transactionId}', 'AnalyticController@datatableTest');
        Route::get('result-label/{testId}', 'AnalyticController@resultlabel');
        // End datatable

        Route::get('transaction/{transactionId}', 'AnalyticController@transaction');
        Route::put('update-result-number/{transactionTestId}', 'AnalyticController@updateResultNumber');
        Route::put('update-result-label/{transactionTestId}', 'AnalyticController@updateResultLabel');
        Route::put('update-result-description/{transactionTestId}', 'AnalyticController@updateResultDescription');

        Route::put('verify-all/{transactionId}', 'AnalyticController@verifyAll');
        Route::put('verify-test/{transactionTestId}', 'AnalyticController@verifyTest');
        Route::put('validate-all/{transactionId}', 'AnalyticController@validateAll');
        Route::put('validate-test/{transactionTestId}', 'AnalyticController@validateTest');
        Route::put('update-test-memo', 'AnalyticController@updateTestMemo');

        Route::get('check-critical-test/{transactionId}', 'AnalyticController@checkCriticalTest');
        Route::put('report-critical-tests', 'AnalyticController@reportCriticalTest');
    });
    // END Analytics

    // BEGIN all route for master data
    Route::get('master/{masterData}', 'MasterController@index');
    Route::post('master/{masterData}/create', 'MasterController@create');
    Route::get('master/{masterData}/edit/{id}', 'MasterController@edit');
    Route::put('master/{masterData}/update', 'MasterController@update');
    Route::delete('master/{masterData}/delete/{id}', 'MasterController@delete');
    // datatable route for master data
    Route::get('master/datatable/{masterData}/{with?}', 'MasterController@datatable');
    Route::get('master/ref-range/datatable', 'MasterController@testRangeDatatable');
    Route::get('master/test-label/datatable', 'MasterController@testLabelDatatable');
    Route::get('master/range/{testId}', 'MasterController@rangeDatatable');
    Route::get('master/result-range/{testId}', 'MasterController@resultRangeDatatable');
    // END all route for master data
    Route::get('master/test-packages/{Ids}', 'MasterController@getTestPackage');

    // for select option form
    Route::get('master/select-options/{masterData}/{searchKey}/{roomType?}', 'MasterController@selectOptions');
});
