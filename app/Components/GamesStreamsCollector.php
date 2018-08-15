<?php

namespace Videostat\Components;

use Illuminate\Support\Facades\Facade;

class GamesStreamsCollector extends Facade
{
    const FACADE_ACCESSOR = 'gss:game_stream_collector';

    protected static function getFacadeAccessor()
    {
        return static::FACADE_ACCESSOR;
    }
}