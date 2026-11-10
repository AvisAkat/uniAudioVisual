@extends('layouts.auth-layout')
@section('pageTitle', 'AVBMS | Register')
@section('content')

    <section class="auth-form-view is-active" id="register-view" aria-labelledby="register-title">
        <div class="auth-heading">
            <span class="eyebrow">Create your profile</span>
            <h2 id="register-title">Register for AVBMS</h2>
            <p>Set up your account to request and manage department equipment.</p>
        </div>
        <form id="register-form" method="POST" action="{{ route('auth.register_handler') }}">
            @csrf
            <div class="form-grid two-columns">
                <div class="field-group">
                    <label for="register-name">Full name</label>
                    <input id="register-name" name="name" type="text" autocomplete="name" placeholder="e.g. Alex Morgan"
                        value="{{ old('name') }}" required>
                    <span class="field-error" data-error-for="register-name">
                        @error('name'){{ $message }}@enderror
                    </span>
                </div>
                <div class="field-group">
                    <label for="register-student-id">Student ID</label>
                    <input id="register-student-id" name="university_id" type="text" placeholder="e.g. 10450123"
                        value="{{ old('university_id') }}" required maxlength="8">
                    <span class="field-error" data-error-for="register-student-id">
                        @error('university_id'){{ $message }}@enderror
                    </span>
                </div>
            </div>
            <div class="field-group">
                <label for="register-email">Email address</label>
                <input id="register-email" name="email" type="email" autocomplete="email" placeholder="name@university.edu"
                    value="{{ old('email') }}" required>
                <span class="field-error" data-error-for="register-email">
                    @error('email'){{ $message }}@enderror
                </span>
            </div>
            <div class="form-grid two-columns">
                <div class="field-group">
                    <label for="register-password">Password</label>
                    <div class="password-field">
                        <input id="register-password" name="password" type="password" autocomplete="new-password"
                            placeholder="At least 8 characters" minlength="8" >
                        <button class="password-toggle" type="button" data-password-toggle="register-password"
                            aria-label="Show password">
                            <span data-icon="eye"><svg class="icon " viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                            </span>
                        </button>
                    </div><span class="field-error"
                        data-error-for="register-password">@error('password'){{ $message }}@enderror</span>
                </div>
                <div class="field-group"><label for="register-confirm">Confirm password</label>
                    <input id="register-confirm" name="password_confirmation" type="password" autocomplete="new-password"
                        placeholder="Repeat password" required ><span class="field-error"
                        data-error-for="register-confirm"></span>
                </div>
            </div>
            <button class="button button-primary button-full button-large" type="submit">
                <span>Create account</span>
                <span data-icon="arrow-right"><svg class="icon " viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M5 12h14M13 6l6 6-6 6"></path>
                    </svg>
                </span>
            </button>
        </form>
        <p class="auth-switch">Already registered?
            <a href="{{ route('auth.login') }}">
                <button type="button" data-show-auth="login">Sign in instead</button>
            </a>
        </p>
    </section>

@endsection