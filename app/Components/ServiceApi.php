<?php

namespace Videostat\Components;

use Illuminate\Support\Facades\Facade;

class ServiceApi extends Facade
{
    const FACADE_ACCESSOR = 'gss:service_api';

    protected static function getFacadeAccessor()
    {
        return static::FACADE_ACCESSOR;
    }
}