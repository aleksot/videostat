<?php

namespace Videostat\Database\Models\Eloquent;

use Illuminate\Database\Eloquent\Model;

use Videostat\Contracts\Database\Models\GameServiceQueue as GameServiceQueueContract;

class GameServiceQueue extends Model implements GameServiceQueueContract
{
    protected $table = 'games_services_queue';
}
