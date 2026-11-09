<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
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
