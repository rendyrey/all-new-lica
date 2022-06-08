<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DataTables;
use DB;
use Illuminate\Support\Carbon;
use Auth;

class PostAnalyticController extends Controller
{
    const STATUS = 2;
    public function index()
    {
        $data['title'] = 'Post Analytics';
        return view('dashboard.post_analytics.index', $data);
    }

    public function datatable($startDate = null, $endDate = null)
    {
        // if the startDate and the endDate not set, the query will be only for today's transactions
        if ($startDate == null && $endDate == null) {
            $from = Carbon::today()->addHours(0)->addMinutes(0)->addSeconds(0)->toDateTimeString();
            $to = Carbon::today()->addHours(23)->addMinutes(59)->addSeconds(59)->toDateTimeString();
            $model = \App\Transaction::selectRaw('transactions.*, transactions.id as t_id')->where('created_time', '>=', $from)
                ->where('created_time', '<=', $to)
                ->where('status', PostAnalyticController::STATUS)
                ->orderBy('cito','desc');
            
            return DataTables::of($model)
            ->addIndexColumn()
            ->escapeColumns([])
            ->make(true);
        }

        // if the startDate and endDate is set, the query will be depend on the given date.
        $from = Carbon::parse($startDate)->addHours(0)->addMinutes(0)->addSeconds(0)->toDateTimeString();
        $to = Carbon::parse($endDate)->addHours(23)->addMinutes(59)->addSeconds(59)->toDateTimeString();
        $model = \App\Transaction::selectRaw('transactions.*, transactions.id as t_id')->where('created_time', '>=', $from)
            ->where('created_time', '<=', $to)
            ->where('status', PostAnalyticController::STATUS)
            ->orderBy('cito','desc');

        return DataTables::of($model)
            ->addIndexColumn()
            ->escapeColumns([])
            ->make(true);
    }
}
