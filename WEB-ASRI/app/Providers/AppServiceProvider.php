<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Notifikasi;
use Illuminate\Support\Facades\Auth;

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
    public function boot()
    {
        View::composer('*', function ($view) {
            if (Auth::check()) {
                $notifData = Notifikasi::where('user_id', Auth::id())
                    ->where('is_read', false)
                    ->orderBy('created_at', 'desc')
                    ->get();

                $view->with([
                    'navbarNotifications' => $notifData->take(5), // 5 terbaru untuk list
                    'unreadCount' => $notifData->count()        // Total angka untuk badge
                ]);
            }
        });
    }
}
