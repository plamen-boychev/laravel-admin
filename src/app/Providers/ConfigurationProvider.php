<?php

namespace LAdmin\Providers;

use Exception;

class ConfigurationProvider extends BaseProvider
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
        # Publishing the packages config files
        $this->publishes([
            __DIR__.'/../../config/ladmin/config.php'       => config_path('ladmin/config.php'),
            __DIR__.'/../../config/ladmin/routes.php'       => config_path('ladmin/routes.php'),
            __DIR__.'/../../config/ladmin/form-items.php'   => config_path('ladmin/form-items.php'),
            __DIR__.'/../../config/ladmin/list-columns.php' => config_path('ladmin/list-columns.php'),
            __DIR__.'/../../config/ladmin/lists.php'        => config_path('ladmin/lists.php'),
        ], 'config');
    }

}
