<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Models\RentalItem;
use App\Observers\RentalItemObserver;

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
        RentalItem::observe(RentalItemObserver::class);
    }
}
