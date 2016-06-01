<?php

namespace LAdmin;

use Illuminate\Support\Facades\Facade;

class ListView extends Facade
{
    /**
     * Get the binding in the IoC container
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'laravel-admin.list-view';
    }
}
