<?php

namespace Videostat\Database\Models\Eloquent;

use Illuminate\Database\Eloquent\Model;

use Videostat\Contracts\Database\Models\GameService as GameServiceContract;

class GameService extends Model implements GameServiceContract
{
    protected $table = 'games_services';
}
