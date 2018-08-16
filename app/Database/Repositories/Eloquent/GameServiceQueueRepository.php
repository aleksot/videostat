<?php

namespace Videostat\Database\Repositories\Eloquent;

use Illuminate\Support\Facades\DB;

use Videostat\Contracts\Database\Models\GameService;
use Videostat\Contracts\Database\Models\GameServiceQueue;
use Videostat\Contracts\Database\Repositories\GameServiceQueueRepository as Contract;

class GameServiceQueueRepository implements Contract
{
    const LOCK_ID_UNSET = 0;

    protected $connection_id;

    protected $game_service_queue;

    public function __construct(GameServiceQueue $game_service_queue)
    {
        $this->game_service_queue = $game_service_queue;
        $this->game_service_queue->setConnection('videostat');
    }

    public function findLocked($lock_value, $limit = 50)
    {
        if ($lock_id = $this->getLockId()) {
            $result = $this->game_service_queue
                ->where('lock_id', $lock_id)
                ->where('lock_value', $lock_value)
                ->limit($limit)
                ->get();
        } else {
            $result = null;
        }

        return $result;
    }

    public function acquireLock($lock_value, $limit = 50)
    {
        if ($lock_id = $this->getLockId()) {
            $query = "
            INSERT IGNORE INTO " . $this->game_service_queue->getTable() . " 
                (`games_services_id`, `lock_id`, `lock_value`, `created_at`, `updated_at`)
            SELECT
                `gs`.`id` `games_services_id`,
                :lock_id `lock_id`,
                :lock_value_select `lock_value`,
                NOW() `created_at`,
                NOW() `updated_at`
            FROM
                `games_services` `gs`
                LEFT JOIN " . $this->game_service_queue->getTable() . " `gsq` 
                    ON `gsq`.`games_services_id` = `gs`.`id` 
                        AND `lock_value` = :lock_value_condition
            WHERE
                `gs`.`is_active` = :is_active
                AND `gsq`.`id` IS NULL  
            LIMIT :limit";

            $lock_value = (int)$lock_value;

            $result = DB::connection($this->game_service_queue->getConnectionName())->insert($query, [
                ':lock_id' => $lock_id,
                ':lock_value_select' => $lock_value,
                ':lock_value_condition' => $lock_value,
                ':is_active' => GameService::IS_ACTIVE_ACTIVE,
                ':limit' => (int)$limit,
            ]);
        } else {
            $result = false;
        }

        return $result;
    }

    public function releaseLock($lock_value, $limit = 50)
    {
        if ($lock_id = $this->getLockId()) {
            $result = $this->game_service_queue
                ->where('lock_id', $lock_id)
                ->where('lock_value', $lock_value)
                ->limit($limit)
                ->update(['lock_id' => static::LOCK_ID_UNSET]);
        } else {
            $result = false;
        }

        return $result;
    }

    protected function getLockId()
    {
        return static::getConnectionId();
    }

    protected function getConnectionId()
    {
        if (!isset($this->connection_id)) {
            $this->connection_id = (int)array_get(DB::connection($this->game_service_queue->getConnectionName())->selectOne('SELECT CONNECTION_ID() `id`'), 'id', 0);
        }

        return $this->connection_id;
    }
}