<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\ActivityLog;
use Auth;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function logActivity($action = '', $description = '')
    {
        if (config('activity_log')) {
            return;
        }
        
        $user = Auth::user();
        ActivityLog::create([
            'user_id' => $user->id,
            'action' => $user->name . ' ' . $action,
            'description' => $description
        ]);
    }

    public function badgeInfo()
    {
        $today = date('Y-m-d');
        $data['pre_analytics'] = \App\Transaction::where('created_at', '>', $today)->where('status', PreAnalyticController::STATUS)->count();
        $data['analytics'] = \App\Transaction::where('created_at', '>', $today)->where('status', AnalyticController::STATUS)->count();

        return response()->json($data);
    }
}
