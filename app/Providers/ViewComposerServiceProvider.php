<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Kategori;

class ViewComposerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        View::composer('*', function ($view) {
            $kategoriler = Kategori::whereNull('ust_kategori_id')
                ->with('altKategoriler')
                ->get();
            
            $view->with('kategoriler', $kategoriler);
        });
    }
}
