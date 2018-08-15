<?php

namespace Videostat\Components;

use Illuminate\Support\Facades\Facade;

class StreamApi extends Facade
{
    const FACADE_ACCESSOR = 'gss:api';

    protected static function getFacadeAccessor()
    {
        return static::FACADE_ACCESSOR;
    }
}