@extends('layouts.auth-layout')
@section('pageTitle', 'AVBMS | Login')
@section('content')

    <section class="auth-form-view is-active" id="login-view" aria-labelledby="login-title">
        <div class="auth-heading">
            <span class="eyebrow">Welcome back</span>
            <h2 id="login-title">Sign in to your workspace</h2>
            <p>Use your university credentials to manage equipment bookings.</p>
        </div>
        <form id="login-form" novalidate="">
            <div class="field-group">
                <label for="login-email">Email address</label>
                <div class="input-with-icon"><span data-icon="mail"><svg class="icon " viewBox="0 0 24 24"
                            aria-hidden="true">
                            <rect x="3" y="5" width="18" height="14" rx="2"></rect>
                            <path d="m3 7 9 6 9-6"></path>
                        </svg></span><input id="login-email" name="email" type="email" autocomplete="email"
                        placeholder="name@university.edu" required=""></div>
                <span class="field-error" data-error-for="login-email"></span>
            </div>
            <div class="field-group">
                <div class="label-row"><label for="login-password">Password</label><span class="muted-label">Case
                        sensitive</span></div>
                <div class="input-with-icon"><span data-icon="lock"><svg class="icon " viewBox="0 0 24 24"
                            aria-hidden="true">
                            <rect x="4" y="10" width="16" height="11" rx="2"></rect>
                            <path d="M8 10V7a4 4 0 0 1 8 0v3M12 14v3"></path>
                        </svg></span><input id="login-password" name="password" type="password"
                        autocomplete="current-password" placeholder="Enter your password" required=""><button
                        class="password-toggle" type="button" data-password-toggle="login-password"
                        aria-label="Show password"><span data-icon="eye"><svg class="icon " viewBox="0 0 24 24"
                                aria-hidden="true">
                                <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg></span></button></div>
                <span class="field-error" data-error-for="login-password"></span>
            </div>
            <button class="button button-primary button-full button-large" type="submit"><span>Sign in</span><span
                    data-icon="arrow-right"><svg class="icon " viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M5 12h14M13 6l6 6-6 6"></path>
                    </svg></span></button>
        </form>
        
        <p class="auth-switch">New to AVBMS? 
           <a href="{{ route('auth.register') }}"> <button type="button" data-show-auth="register">Create an account</button></a>
        </p>
    </section>


@endsection