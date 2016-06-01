<?php

namespace LAdmin\Providers;

use Exception;

class MigrationsProvider extends BaseProvider
{

    public function register()
    {
    }

    /**
     * Publishing the resources related to the database - migration scripts,
     * factories and seed classes
     *
     * @return void
     */
    public function boot()
    {
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

}
