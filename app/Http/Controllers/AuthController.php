<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

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
                'title' => 'Account Created',
                'type' => 'success',
                'message' => 'Your account has been created successfully.',
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
}
