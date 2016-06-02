<?php

namespace LAdmin\Facades;

use Illuminate\Support\Facades\Facade;

class ListColumn extends Facade
{

    /**
     * Get the binding in the IoC container
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'laravel-admin.list-column';
    }

}
