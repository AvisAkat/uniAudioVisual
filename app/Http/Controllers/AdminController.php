<?php

namespace App\Http\Controllers;

class AdminController extends Controller
{
    public function showDashboard()
    {

        $data = [
            'pageTitle' => 'Dashboard | AVBMS',
            'appInfo' => [
                'appTitle' => 'Dashboard',
                'appDescription' => 'Your booking workspace at a glance',
            ],
        ];

        return view('pages.admin.dashboard', $data);
    }

    public function activityLog()
    {

        $data = [
            'pageTitle' => 'Activity Log | AVBMS',
            'appInfo' => [
                'appTitle' => 'Activity Log',
                'appDescription' => 'A chronological audit of important system events',
            ],
        ];

        return view('pages.admin.activity-log', $data);
    }

    public function manageUsers()
    {

        $data = [
            'pageTitle' => 'User Management | AVBMS',
            'appInfo' => [
                'appTitle' => 'User Management',
                'appDescription' => 'Manage user accounts and their access privileges',
            ],
        ];

        return view('pages.admin.user-management', $data);
    }
}
