<?php

namespace Videostat\Database\Repositories\Eloquent;

use Videostat\Contracts\Database\Models\GameService;
use Videostat\Contracts\Database\Repositories\GameServiceRepository as Contract;

class GameServiceRepository implements Contract
{
    protected $game_service;

    public function __construct(GameService $game_service)
    {
        $this->game_service = $game_service;
        $this->game_service->setConnection('videostat');
    }

    public function find($id)
    {
        $game_service = $this->game_service;

        return $game_service->where($game_service->getKeyName(), $id)->first();
    }
}