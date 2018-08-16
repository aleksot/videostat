<?php

namespace Videostat\Database\Repositories\Eloquent;

use Videostat\Contracts\Database\Models\Game;
use Videostat\Contracts\Database\Repositories\GameRepository as Contract;

class GameRepository implements Contract
{
    protected $game;

    public function __construct(Game $game)
    {
        $this->game = $game;
        $this->game->setConnection('videostat');
    }

    public function find($id)
    {
        return $this->game->where($this->game->getKeyName(), $id)->first();
    }

    public function findAll($ids)
    {
        return $this->game->whereIn($this->game->getKeyName(), $ids)->get();
    }
}