<?php

namespace Videostat\Contracts\Database\Repositories;

use Videostat\Contracts\Database\Models\Game;

interface GameServiceRepository
{
    public function find($id);
    public function findActiveForGame(Game $game);
}