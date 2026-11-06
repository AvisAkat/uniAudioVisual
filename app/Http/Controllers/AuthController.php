<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{

    public function showLoginForm()
    {
        return view('pages.auth.login');
    }

    public function showRegisterForm()
    {
        return view('pages.auth.register');
    }

}
