<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DataTables;
use DB;

class PreAnalyticController extends Controller
{
    public function index()
    {
        $data['title'] = 'Pre Analytics';
        return view('dashboard.pre_analytics.index', $data);
    }

    public function datatable()
    {
        $model = \App\Transaction::query();
        return DataTables::of($model)
        ->addIndexColumn()
        ->escapeColumns([])
        ->make(true);
    }

    public function datatableTest($roomClass)
    {
        $model = \App\TestPreAnalyticsView::where('class', $roomClass)
            ->orWhere('class', '0')
            ->orWhereNull('class');
        return DataTables::of($model)
        ->addIndexColumn()
        ->escapeColumns([])
        ->make(true);
    }

    public function datatableSelectTest(Request $request, $roomClass, $withoutIds)
    {
        $model = \App\TestPreAnalyticsView::where(function($q) use($roomClass){
            $q->where('class',$roomClass)->orWhere('class','0')->orWhereNull('class');
        })->whereNotIn('unique_id', explode(',',$withoutIds));
        return DataTables::of($model)
        ->addIndexColumn()
        ->escapeColumns([])
        ->make(true);
    }
}
