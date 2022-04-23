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
        // $modelTest = \App\Test::selectRaw('tests.name as name, prices.price as price, "single" as type')
        //     ->leftJoin('prices','tests.id','=','prices.test_id')
        //     ->where('prices.class', $roomClass);
        // $allModel = \App\Package::selectRaw('packages.name as name, prices.price as price, "package" as type')
        //     ->leftJoin('prices','packages.id','=','prices.package_id')
        //     ->where('prices.class', $roomClass)->union($modelTest);
        $model = \App\TestPreAnalyticsView::where('class', $roomClass);
        return DataTables::of($model)
        ->addIndexColumn()
        ->escapeColumns([])
        ->make(true);
    }
}
