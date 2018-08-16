<?php

namespace Videostat\Services;

use Videostat\Components\ServiceApi;
use Videostat\Contracts\Database\Repositories\GameServiceQueueRepository;
use Videostat\Contracts\Database\Repositories\GameServiceRepository;
use Videostat\Contracts\Database\Repositories\ServiceRepository;
use Videostat\Contracts\Database\Repositories\GameStreamStatRepository;

class GamesStreamsCollectorService
{
    protected $game_service_queue_repository;
    protected $game_service_repository;
    protected $service_repository;
    protected $game_stream_stat_repository;
    protected $minute_multiplicity;
    protected $current_minute;

    public function __construct(
        GameServiceQueueRepository $game_service_queue_repository,
        GameServiceRepository $game_service_repository,
        ServiceRepository $service_repository,
        GameStreamStatRepository $game_stream_stat_repository
    ) {
        $this->game_service_queue_repository = $game_service_queue_repository;
        $this->game_service_repository = $game_service_repository;
        $this->service_repository = $service_repository;
        $this->game_stream_stat_repository = $game_stream_stat_repository;
    }

    public function run($limit = 50)
    {
        $result = false;

        if ($this->canRun()) {
            $lock_value = $this->getCurrentMinuteDivision();

            $game_service_queue_repository = $this->game_service_queue_repository;

            if ($game_service_queue_repository->acquireLock($lock_value, $limit)) {
                $queue_items = $game_service_queue_repository->findLocked($lock_value, $limit);

                if ($queue_items) {
                    $this->handle($queue_items);

                    $game_service_queue_repository->releaseLock($lock_value, $limit);
                    $result = true;
                }
            }
        }

        return $result;
    }

    protected function handle($queue_items)
    {
        foreach ($queue_items as $queue_item) {
            $game_service_id = array_get($queue_item, 'games_services_id');

            if (empty($game_service_id)) {
                continue;
            }

            $game_service = $this->game_service_repository->find($game_service_id);
            $service_id = array_get($game_service, 'service_id');

            if (empty($service_id)) {
                continue;
            }

            $service = $this->service_repository->find($service_id);

            try {
                $driver = ServiceApi::getInstance($service);
            } catch (\Exception $exception) {
                $driver = null;
            }

            if (empty($driver))
                continue;

            $offset_streams = 0;
            $limit_streams = 10000;

            while ($streams = $driver->getActiveStreamsForGameService($game_service, $limit_streams, $offset_streams)) {
                $this->game_stream_stat_repository->collect($game_service, $streams);
                $offset_streams += $limit_streams;
            }
        }
    }

    protected function canRun()
    {
        return !$this->getCurrentMinuteReminder();
    }

    protected function getCurrentMinuteDivision()
    {
        return (int)ceil($this->getCurrentMinute() / $this->getMinuteMultiplicity());
    }

    protected function getCurrentMinuteReminder()
    {
        return (int)($this->getCurrentMinute() % $this->getMinuteMultiplicity());
    }

    protected function getCurrentMinute()
    {
        if (!isset($this->current_time)) {
            if (defined('LARAVEL_START') && LARAVEL_START > 0) {
                $time = LARAVEL_START;
            } elseif (empty($_SERVER['REQUEST_TIME'])) {
                $time = time();
            } else {
                $time = array_get($_SERVER, 'REQUEST_TIME');
            }

            $this->current_minute = ceil($time / 60);
        }

        return $this->current_minute;
    }

    protected function getMinuteMultiplicity()
    {
        if (!isset($this->minute_multiplicity)) {
            $this->minute_multiplicity = env('GSS_QUEUE_MINUTE_MULTIPLICITY', 1);
        }

        return $this->minute_multiplicity;
    }
}