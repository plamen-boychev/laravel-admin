<?php

namespace LAdmin;

use Illuminate\Support\ServiceProvider;

class GeneralServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        # Publishing the packages config files
        $this->publishes([
            __DIR__.'/../../config/ladmin.php' => config_path('ladmin.php'),
        ], 'config');

        # Publishing migrations
        $this->publishes([
            __DIR__.'/../../database/migrations/' => database_path('migrations'),
        ], 'migrations');

        # Publishing factories
        $this->publishes([
            __DIR__.'/../../database/factories/' => database_path('factories'),
        ], 'factories');

        # Publishing seeds
        $this->publishes([
            __DIR__.'/../../database/seeds/' => database_path('seeds'),
        ], 'seeds');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
