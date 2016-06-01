<?php

namespace LAdmin\Providers;

use Exception;

class RootsProvider extends BaseProvider
{

    public function register()
    {
    }

    /**
     * Registering all package routes
     *
     * @return void
     */
    public function boot()
    {
        # Registering the routes
        if (! $this->app->routesAreCached()) {
            # Registering routes for static actions
            $this->registerStaticRoutes();

            # Registering routes dynamic actions:
            #   - for registered models administration
            $this->registerDynamicModelRoutes();
        }
    }

    /**
     * Registering routes for static actions
     *
     * @return void
     */
    private function registerStaticRoutes()
    {
        $this->loadConfigurationFileOrDefault('ladmin/routes');
        $this->requireFile('app/HTTP/routes');
    }

    /**
     * Registering routes dynamic actions:
     *   - for registered models administration
     *
     * @todo   Load all configured administration models and build routes for those
     *
     * @return void
     */
    private function registerDynamicModelRoutes()
    {
    }

}
