<?php

namespace Videostat\Services\Adapters\Service;

use TwitchApi\TwitchApi;

use Videostat\Contracts\Database\Models\GameService;

class Twitch
{
    const API_STREAM_TYPE_LIVE = 'live';
    const TWITCH_API_MAX_COUNT_PER_CHUNK = 100;

    protected $api;

    public function __construct(TwitchApi $api)
    {
        $this->api = $api;
    }

    public function getActiveStreamsForGameService(GameService $game_service, $limit = 25, $offset = 0)
    {
        $this->init();

        $limit_mormalized = $limit > static::TWITCH_API_MAX_COUNT_PER_CHUNK
            ? static::TWITCH_API_MAX_COUNT_PER_CHUNK
            : $limit;

        try {
            $result = [];

            do {
                $res = $this->api->getLiveStreams(
                    null,
                    array_get($game_service, 'external_game_code'),
                    null,
                    static::API_STREAM_TYPE_LIVE,
                    $limit_mormalized,
                    $offset
                );

                $res = (array)array_get($res, 'streams', []);

                if (empty($res)) {
                    break;
                }

                foreach ($res as $key => $item) {
                    foreach (['_id', 'viewers'] as $required_field) {
                        if (empty($item[$required_field]))
                            break 2;
                    }

                    $result[] = [
                        'id' => array_get($item, '_id'),
                        'viewers' => array_get($item, 'viewers'),
                    ];

                    unset($res[$key]);
                }

                $offset += $limit_mormalized;
                $limit -= $limit_mormalized;
            } while ($limit > 0);
        } catch (\Exception $exception) {
            $result = [];
        }

        return $result;
    }

    protected function init()
    {
        $this->api->setReturnJson(false);
        $this->api->setApiVersion(5);
    }
}