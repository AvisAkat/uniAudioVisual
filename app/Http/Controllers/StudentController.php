<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function showDashboard()
    {

        $data = [
            'pageTitle' => 'Dashboard | AVBMS',
            'appTitle' => 'Dashboard'
        ];
        
        return view('pages.student.dashboard', $data);
    }
}
