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
}
