<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PreAnalyticController extends Controller
{
    public function index()
    {
        $data['title'] = 'Pre Analytics';
        return view('dashboard.pre_analytics.index', $data);
    }
}
