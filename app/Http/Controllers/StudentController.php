<?php

namespace App\Http\Controllers;

class StudentController extends Controller
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

        return view('pages.student.dashboard', $data);
    }

    public function equipments()
    {

        $data = [
            'pageTitle' => 'Browse Equipment | AVBMS',
            'appInfo' => [
                'appTitle' => 'Browse Equipment',
                'appDescription' => 'Find equipment and check stock for a specific time slot',
            ],
        ];

        return view('pages.student.browse-equipment', $data);
    }

    public function equipmentAvailability()
    {

        $data = [
            'pageTitle' => 'Check Availability | AVBMS',
            'appInfo' => [
                'appTitle' => 'Check Availability',
                'appDescription' => 'Compare equipment capacity across every time slot',
            ],
        ];

        return view('pages.student.equipment-availability', $data);
    }

    public function myBookings()
    {

        $data = [
            'pageTitle' => 'My Bookings | AVBMS',
            'appInfo' => [
                'appTitle' => 'My Bookings',
                'appDescription' => 'Track requests, view decisions and manage upcoming reservations',
            ],
        ];

        return view('pages.student.bookings', $data);
    }
}
