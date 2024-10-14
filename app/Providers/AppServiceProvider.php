<?php

namespace App\Providers;

use App\Http\View\Composers\NotificationComposer;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\Transfer;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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
        View::composer('admin.layout.header', function ($view) {
            if (Auth::check()) {
                $id = Auth::id();
                $products = Product::where('user_id', $id)->orderByDesc('created_at')->get();
                $view->with('products', $products);
            }
        });
        // Composer cho admin layout
        View::composer('admin.layout.header', function ($view) {
            if (Auth::check()) {
                $id = Auth::id();
                $adminNotifications = Transaction::orderByDesc('created_at')
                    ->where(function ($query) {
                        $query->where('notification', 0)
                            ->orWhere('notification', 2);
                    })
                    ->where('user_id', $id)
                    ->get();

                // Truyền biến vào view
                $view->with('adminNotifications', $adminNotifications);
            }
        });

        View::composer('admin.layout.header', function ($view) {
            if (Auth::check()) {
                $id = Auth::id();
                $adminTransferNotifications = Transfer::orderByDesc('created_at')
                    ->where('notification', 1)->where('user_id', $id)->get();
                $view->with('adminTransferNotifications', $adminTransferNotifications);
            }
        });

        // Composer cho superadmin layout
        View::composer('superadmin.layout.header', function ($view) {
            if (Auth::check()) {
                $id = Auth::id();
                $superAdminNotifications = Transaction::orderByDesc('created_at')
                    ->where('notification', 1)
                    ->get();

                // Truyền biến vào view
                $view->with('superAdminNotifications', $superAdminNotifications);
            }
        });
    }
}
