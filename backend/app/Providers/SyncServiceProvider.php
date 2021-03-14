<?php

namespace App\Providers;

use App\Models\CastMember;
use App\Models\Category;
use App\Models\Genre;
use App\Observers\SyncModelObserver;
use Illuminate\Support\ServiceProvider;

class SyncServiceProvider extends ServiceProvider {
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register() {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot() {

        if (env('SYNC_RABBITMQ_ENABLED') !== true) {
            return;
        }

        Genre::observe(SyncModelObserver::class);
        Category::observe(SyncModelObserver::class);
        CastMember::observe(SyncModelObserver::class);
    }
}
