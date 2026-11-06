<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm(): View
    {
        return view('pages.auth.login');
    }

    public function showRegisterForm(): View
    {
        return view('pages.auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'university_id' => ['required', 'digits:8', 'max:8', Rule::unique(User::class, 'university_id')],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique(User::class, 'email')],
            'password' => ['required', 'string', 'confirmed', Password::defaults() ?? Password::min(8)],
        ], [], [
            'university_id' => 'student ID',
        ]);

        try {
            $created = User::create([
                'name' => $validated['name'],
                'university_id' => $validated['university_id'],
                'email' => $validated['email'],
                'password' => $validated['password'],
            ]);
        } catch (\Exception $e) {
            return back()->withInput()->with('toast', [
                'title' => 'Account Creation Failed',
                'type' => 'error',
                'message' => 'An error occurred while creating your account. Please try again later.',
            ]);
        }

        if (!$created) {
            return back()->withInput()->with('toast', [
                'title' => 'Account Creation Failed',
                'type' => 'error',
                'message' => 'An error occurred while creating your account. Please try again later.',
            ]);
        } else {

            return to_route('auth.loginForm')
                ->with('toast', [
                    'title' => 'Account Created',
                    'type' => 'success',
                    'message' => 'Your account has been created successfully.',
                ]);
        }
    }

    public function login(Request $request): RedirectResponse
    {
        // checking wheather the login is an email or a username
        $fieldType = filter_var($request->login_id, FILTER_VALIDATE_EMAIL) ? 'email' : 'university_id';

        if ($fieldType == 'email') {
            $request->validate([
                'login_id' => 'required|email|exists:users,email',
                'password' => 'required',
            ], [
                'login_id.required' => 'Enter your email or ID',
                'login_id.email' => 'Invalid email address',
                'login_id.exists' => 'No account found for this email',
            ]);
        } else {
            $request->validate([
                'login_id' => 'required|exists:users,university_id',
                'password' => 'required',
            ], [
                'login_id.required' => 'Enter your email or ID',
                'login_id.exists' => 'No account found for this ID',
            ]);
        }

        $creds = [
            $fieldType => $request->login_id,
            'password' => $request->password,
        ];

        if (Auth::attempt($creds)) {

            if ($fieldType == 'email') {
                $user = User::where('email', $request->login_id)->first();
            } else {
                $user = User::where('university_id', $request->login_id)->first();
            }

            // redirect the student dashboard if the user is a student
            if($user->role == 'student') {
                return redirect()->route('student.dashboard')->with('toast', [
                    'title' => 'Login Successful',
                    'type' => 'success',
                    'message' => 'Welcome back, '.Auth::user()->name.'!',
                ]);
            }

            // redirect the staff dashboard if the user is a staff member
            if($user->role == 'staff') {
                return redirect()->route('staff.dashboard')->with('toast', [
                    'title' => 'Login Successful',
                    'type' => 'success',
                    'message' => 'Welcome back, '.Auth::user()->name.'!',
                ]);
            }

            // redirect the student dashboard as default to dashboard
            return redirect()->route('student.dashboard')->with('toast', [
                'title' => 'Login Successful',
                'type' => 'success',
                'message' => 'Welcome back, ' . Auth::user()->name . '!',
            ]);

        } else {
            return redirect()->route('auth.login')->withInput()->with('toast', [
                'title' => 'Login Failed',
                'type' => 'error',
                'message' => 'Incorrect email or password.',
            ]);
        }
    }
}
