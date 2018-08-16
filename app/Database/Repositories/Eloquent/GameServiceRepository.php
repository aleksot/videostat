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

    public function findForGames($games)
    {
        $game_service = $this->game_service;

        $games_ids = [];

        foreach ($games as $game)
            $games_ids[$game->id] = 1;

        if ($games_ids) {
            $result = $game_service
                ->whereIn('game_id', array_keys($games_ids))
                ->get();
        } else {
            $result = [];
        }

        return $result;
    }
}