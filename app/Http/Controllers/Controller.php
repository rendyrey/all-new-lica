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
}
