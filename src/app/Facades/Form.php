<?php

namespace LAdmin\Facades;

use Illuminate\Support\Facades\Facade;

class Form extends Facade
{

    /**
     * Get the binding in the IoC container
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'laravel-admin.form';
    }

}
