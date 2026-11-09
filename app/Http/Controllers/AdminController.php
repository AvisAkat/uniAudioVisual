<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{

    public function showDashboard()
    {

        $data = [
            'pageTitle' => 'Dashboard | AVBMS',
            'appInfo' => [
                'appTitle' => 'Dashboard',
                'appDescription' => 'Your booking workspace at a glance'
                ]
        ];
        
        return view('pages.admin.dashboard', $data);
    }

    public function activityLog()
    {
        
        $data = [
            'pageTitle' => 'Activity Log | AVBMS',
            'appInfo' => [
                'appTitle' => 'Activity Log',
                'appDescription' => 'A chronological audit of important system events'
                ]
        ];
        
        return view('pages.admin.activity-log', $data);
    }
}
