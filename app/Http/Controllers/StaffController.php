<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StaffController extends Controller
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
        
        return view('pages.staff.dashboard', $data);
    }

    public function bookingRequest()
    {
        
        $data = [
            'pageTitle' => 'Booking Request | AVBMS',
            'appInfo' => [
                'appTitle' => 'Booking Management',
                'appDescription' => 'Review, approve and process student equipment requests'
                ]
        ];
        
        return view('pages.staff.booking-request', $data);
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
        
        return view('pages.staff.activity-log', $data);
    }

    public function inventory()
    {
        
        $data = [
            'pageTitle' => 'Inventory | AVBMS',
            'appInfo' => [
                'appTitle' => 'Inventory Management',
                'appDescription' => 'Maintain stock quantities, conditions and live availability'
                ]
        ];
        
        return view('pages.staff.inventory', $data);
    }

    public function timeSlot()
    {
        
        $data = [
            'pageTitle' => 'Time Slots & Calendar | AVBMS',
            'appInfo' => [
                'appTitle' => 'Time Slots & Calendar',
                'appDescription' => 'Inspect occupancy and available stock by date and time'
                ]
        ];
        
        return view('pages.staff.time-slots-calendar', $data);
    }

    public function usageHistory()
    {
        
        $data = [
            'pageTitle' => 'Usage History | AVBMS',
            'appInfo' => [
                'appTitle' => 'Usage History',
                'appDescription' => 'Track equipment collection, return status and processing staff'
                ]
        ];
        
        return view('pages.staff.usage-history', $data);
    }
}
