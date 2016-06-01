<?php

namespace LAdmin\Providers;

use Exception;

class FacadeProvider extends BaseProvider
{

    public function register()
    {
        $this->app->bind('laravel-admin.table', function() {
            return new \LAdmin\Package\Table\TableService;
        });
        $this->app->bind('laravel-admin.form', function() {
            return new \LAdmin\Package\Form\FormService;
        });
        $this->app->bind('laravel-admin.form-item', function() {
            return new \LAdmin\Package\FormItem\FormItemService;
        });
        $this->app->bind('laravel-admin.list-view', function() {
            return new \LAdmin\Package\ListView\ListViewService;
        });
        $this->app->bind('laravel-admin.list-column', function() {
            return new \LAdmin\Package\ListColumn\ListColumnService;
        });
    }

    /**
     * Registering all events the package is interested in
     *
     * @return void
     */
    public function boot()
    {
    }

}
