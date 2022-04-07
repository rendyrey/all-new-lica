<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PreAnalyticController extends Controller
{
    public function index()
    {
        $data['page'] = 'Pre Analytics';
        return view('dashboard.pre_analytic.index', $data);
    }
}
