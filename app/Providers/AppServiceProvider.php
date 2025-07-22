<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Kategori;

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
        // Global kategoriler view composer
        View::composer('*', function ($view) {
            try {
                $kategoriler = Kategori::whereNull('ust_kategori_id')
                    ->with('altKategoriler')
                    ->get();
                
                $view->with('kategoriler', $kategoriler);
            } catch (\Exception $e) {
                $view->with('kategoriler', collect());
            }
        });
    }
}
