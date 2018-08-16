<?php

namespace Videostat\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Videostat\Contracts\Database\Repositories\GameRepository;

class CheckGame
{
    protected $game_repository;

    public function __construct(GameRepository $game_repository)
    {
        $this->game_repository = $game_repository;
    }

    public function handle(Request $request, Closure $next)
    {
        if ($this->game_repository->find($request->game_id)) {
            return $next($request);
        }

        throw new \InvalidArgumentException('game_not_found');
    }

}