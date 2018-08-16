<?php

namespace Videostat\Contracts\Database\Repositories;

interface GameServiceRepository
{
    public function find($id);
    public function findActiveForGames($games);
}