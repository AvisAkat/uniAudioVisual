<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StudentController extends Controller
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
        
        return view('pages.student.dashboard', $data);
    }
}
