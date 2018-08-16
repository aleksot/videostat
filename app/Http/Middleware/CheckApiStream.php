<?php

namespace Videostat\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Videostat\Contracts\Database\Repositories\GameRepository;

class CheckApiStream
{
    protected $game_repository;

    public function __construct(GameRepository $game_repository)
    {
        $this->game_repository = $game_repository;
    }

    public function handle(Request $request, Closure $next)
    {
        $games_ids = trim($request->games_ids);

        if ($games_ids) {
            $game_ids = explode(',', $games_ids);

            foreach ($game_ids as &$game_id) {
                $game_id = (int)trim($game_id);
            }

            unset($game_id);

            if ($this->game_repository->findAll($game_ids)) {
                $request->games_ids = $games_ids;

                return $next($request);
            }

            throw new \InvalidArgumentException('games_not_found');
        }

        throw new \InvalidArgumentException('games_required');
    }

}