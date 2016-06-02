<?php

namespace LAdmin\Facades;

use Illuminate\Support\Facades\Facade;

class FormItem extends Facade
{

    /**
     * Get the binding in the IoC container
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'laravel-admin.form-item';
    }

}
