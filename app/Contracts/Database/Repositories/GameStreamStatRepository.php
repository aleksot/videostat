<?php

namespace Videostat\Contracts\Database\Repositories;

use Videostat\Contracts\Database\Models\GameService;

interface GameStreamStatRepository
{
    public function findStreamsListForGamesServices(
        $games_services,
        $period_start,
        $period_end,
        $limit,
        $offset
    );

    public function findViewersForGamesServices($games_services, $period_start, $period_end, $limit, $offset);

    public function collect(GameService $game_service, $streams);
}