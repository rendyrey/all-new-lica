<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DataTables;
use DB;
use Illuminate\Support\Carbon;

class AnalyticController extends Controller
{
    const STATUS = 1;
    /**
     * 
     */
    public function index()
    {
        $data['title'] = 'Analytics';
        return view('dashboard.analytics.index', $data);
    }

    /**
     * 
     */
    public function datatable($startDate = null, $endDate = null)
    {
        // if the startDate and the endDate not set, the query will be only for today's transactions
        if ($startDate == null && $endDate == null) {
            $from = Carbon::today()->addHours(0)->addMinutes(0)->addSeconds(0)->toDateTimeString();
            $to = Carbon::today()->addHours(23)->addMinutes(59)->addSeconds(59)->toDateTimeString();
            $model = \App\Transaction::where('created_time', '>=', $from)->where('created_time', '<=', $to)->where('status', AnalyticController::STATUS)->orderBy('cito','desc');
            
            return DataTables::of($model)
            ->addIndexColumn()
            ->escapeColumns([])
            ->make(true);
        }

        // if the startDate and endDate is set, the query will be depend on the given date.
        $from = Carbon::parse($startDate)->addHours(0)->addMinutes(0)->addSeconds(0)->toDateTimeString();
        $to = Carbon::parse($endDate)->addHours(23)->addMinutes(59)->addSeconds(59)->toDateTimeString();
        $model = \App\Transaction::where('created_time', '>=', $from)->where('created_time', '<=', $to)->where('status', AnalyticController::STATUS)->orderBy('cito','desc');;

        return DataTables::of($model)
            ->addIndexColumn()
            ->escapeColumns([])
            ->make(true);
    }

    /**
     * 
     */
    public function datatableTest(Request $request)
    {
        $model = \App\TransactionTest::where('transaction_id', $request->transaction_id);

        return Datatables::of($model)
            ->addIndexColumn()
            ->escapeColumns([])
            ->make(true);
    }

    /**
     * 
     */
    public function transaction($transactionId)
    {
        try {
            $transaction = \App\Transaction::findOrFail($transactionId);
            
            return response()->json(['message' => 'SUCCESS', 'data' => $transaction]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMesage()], 400);
        }
    }
}
