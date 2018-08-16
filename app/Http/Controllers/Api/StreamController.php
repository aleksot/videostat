<?php

namespace Videostat\Http\Controllers\Api;

use Illuminate\Http\Request;

use Videostat\Contracts\Database\Repositories\GameStreamStatRepository;
use Videostat\Http\Controllers\Controller;
use Videostat\Http\Resources\GameStreamCollection;

use Videostat\Contracts\Database\Repositories\GameRepository;
use Videostat\Contracts\Database\Repositories\GameServiceRepository;

class StreamController extends Controller
{
    public function index(
        Request $request,
        GameRepository $game_repository,
        GameServiceRepository $game_service_repository,
        GameStreamStatRepository $game_stream_stat_repository
    ) {
        $games_services = $game_service_repository->findForGames($game_repository->findAll(explode(',', $request->games_ids)));

        $streams_list = $game_stream_stat_repository->findStreamsListForGamesServices(
            $games_services,
            $request->period_start,
            $request->period_end,
            $request->limit,
            $request->offset
        );

        foreach ($streams_list as $index => $stream) {
            $game_service = $game_service_repository->find(array_get($stream, 'games_services_id'));

            if (empty($game_service)) {
                unset($streams_list[$index]);
                continue;
            }

            unset($streams_list[$index]['games_services_id']);

            $streams_list[$index]['game_id'] = $game_service->game_id;
            $streams_list[$index]['service_id'] = $game_service->service_id;
        }

        return new GameStreamCollection($streams_list);
    }

    public function viewers(
        Request $request,
        GameRepository $game_repository,
        GameServiceRepository $game_service_repository,
        GameStreamStatRepository $game_stream_stat_repository
    ) {
        $games_services = $game_service_repository->findForGames($game_repository->findAll(explode(',', $request->games_ids)));

        $offset = 0;
        $limit = 10000;

        $result = [];

        do {
            $games_services_viewers = $game_stream_stat_repository->findViewersForGamesServices(
                $games_services,
                $request->period_start,
                $request->period_end,
                $limit,
                $offset
            );

            foreach ($games_services_viewers as $index => $game_service_viewer) {
                $game_service = $game_service_repository->find(array_get($game_service_viewer, 'games_services_id'));

                if (empty($game_service)) {
                    unset($games_services_viewers[$index]);
                    continue;
                }

                if (!isset($result[$game_service->id])) {
                    $result[$game_service->id] = [];
                }

                if (!isset($result[$game_service->id]['viewers'])) {
                    $result[$game_service->id]['viewers'] = 0;
                }

                $result[$game_service->id]['viewers'] += array_get($game_service_viewer, 'viewers_count');
                $result[$game_service->id]['game_id'] = $game_service->game_id;
                $result[$game_service->id]['service_id'] = $game_service->service_id;
            }

            $offset += $limit;
        } while (count($games_services_viewers) > 0);

        $result = collect($result)->map(function ($arg) {
            return is_array($arg) ? collect($arg) : $arg;
        });;

        return new GameStreamCollection($result);
    }
}
