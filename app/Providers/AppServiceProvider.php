<?php

namespace App\Providers;

use App\Models\Booking;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();

        view()->composer('layouts.pages-layout', function (View $view): void {
            $user = auth()->user();
            $pendingBookingCount = match ($user?->role) {
                'staff', 'admin' => Booking::query()->where('status', 'Pending')->count(),
                'student' => $user->bookings()->where('status', 'Pending')->count(),
                default => 0,
            };

            $view->with('pendingBookingCount', $pendingBookingCount);
        });
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }
}
