<?php

namespace Videostat\Database\Repositories\Eloquent;

use Illuminate\Support\Facades\DB;

use Videostat\Contracts\Database\Models\GameService;
use Videostat\Contracts\Database\Models\GameStreamStat;
use Videostat\Contracts\Database\Repositories\GameStreamStatRepository as Contract;

class GameStreamStatRepository implements Contract
{
    protected $game_stream_stat;

    public function __construct(GameStreamStat $game_stream_stat)
    {
        $this->game_stream_stat = $game_stream_stat;
        $this->game_stream_stat->setConnection('videostat');
    }

    public function find($id)
    {
        return $this->game->where($this->game->getKeyName(), $id)->first();
    }

    public function findStreamsListForGamesServices(
        $games_services,
        $period_start = null,
        $period_end = null,
        $limit = null,
        $offset = null
    ) {
        if ($limit <= 0)
            $limit = 10000;

        if ($offset <= 0)
            $offset = 0;

        $games_streams_ids = $this->findGamesStreamsIds($games_services, $period_start, $period_end);

        $data = [];

        foreach ($games_streams_ids as $key => $games_streams_id) {
            $shard = $this->getShard($games_streams_id);

            if (!isset($data[$shard]))
                $data[$shard] = [];

            $data[$shard][] = $games_streams_id;
            unset($games_streams_ids[$key]);
        }

        unset($games_streams_ids);

        $result = [];

        foreach ($data as $shard => $games_streams_ids) {
            if (empty($limit))
                break;

            $table_name = sprintf('games_streams_stat_%s', date('Y_m'));

            $query = "
                SELECT
                  `external_stream_id` `id`
                FROM
                  `$table_name` 
                WHERE
                  id IN(" . join(',', array_map('intval', $games_streams_ids)) . ")
                LIMIT :offset, :limit";

            $res = DB::connection("gss_stat_$shard")->select($query, [
                ':offset' => $offset,
                ':limit' => $limit
            ]);

            if ($res) {
                foreach ($res as $key => $params) {
                    $result[array_get($params, 'id')] = $params;
                    unset($res[$key]);
                }
            }

            $cnt = count($result);

            if ($cnt >= $limit)
                break;

            $offset -= $cnt;
            $offset = max($offset, 0);

            $limit-= $cnt;
            $limit = max($limit, 0);
        }

        if ($result) {
            $result = array_slice($result, 0, $limit);
        } else {
            $result = [];
        }

        return collect($result)->map(function ($arg) {
            return is_array($arg) ? collect($arg) : $arg;
        });
    }

    public function findGamesStreamsIds($games_services, $period_start = null, $period_end = null)
    {
        $games_services_ids = [];

        foreach ($games_services as $game_service) {
            $games_services_ids[] = $game_service->id;
        }

        if ($games_services_ids) {
            $table_name = sprintf('gss_stat_bind_%s', date('Y_m'));

            $query = "
            SELECT
              `gss_stat_id` `id`
            FROM
              `$table_name`
            WHERE
              `games_services_id` IN(" . join(', ', array_map('intval', $games_services_ids)) . ")
              AND `created_at` BETWEEN NOW() - INTERVAL 1 DAY AND NOW()";

            $res = DB::connection('gss_stat_bind')->select($query);
        } else {
            $res = false;
        }

        return empty($res) ? [] : array_column($res, 'id');
    }

    public function collect(GameService $game_service, $streams)
    {
        $data = $this->prepareShardData($streams);

        foreach ($data as $shard => $streams_data) {
            $table_name = sprintf('games_streams_stat_%s', date('Y_m'));

            $query = 'CREATE TABLE IF NOT EXISTS `' . $table_name . '` LIKE `games_streams_stat`';

            if ($res = DB::connection("gss_stat_$shard")->statement($query)) {
                $statements = $values = $statements_bind = $values_bind = [];

                foreach ($streams_data as $key => $stream) {
                    $statements[] = "(:id_$key, :external_stream_id_$key, :external_viewers_count_$key)";
                    $statements_bind[] = "(:games_services_id_$key, :gss_stat_id_$key, NOW())";

                    $db_id = array_get($stream, 'db_id');

                    $values[":id_$key"] = $db_id;
                    $values[":external_stream_id_$key"] = array_get($stream, 'id');
                    $values[":external_viewers_count_$key"] = array_get($stream, 'viewers');

                    $values_bind[":games_services_id_$key"] = $game_service->id;
                    $values_bind[":gss_stat_id_$key"] = $db_id;

                    unset($streams_data[$key]);
                }

                if ($statements) {
                    $query = 'INSERT INTO `' . $table_name . '`
                      (`id`, `external_stream_id`, `external_viewers_count`)
                      VALUES ' . join(', ', $statements);

                    DB::connection("gss_stat_$shard")->insert($query, $values);
                }

                if ($statements_bind) {
                    $table_name = sprintf('gss_stat_bind_%s', date('Y_m'));
                    $query = 'CREATE TABLE IF NOT EXISTS `' . $table_name . '` LIKE `gss_stat_bind`';

                    if ($res = DB::connection("gss_stat_bind")->statement($query)) {
                        $query = 'INSERT INTO `' . $table_name . '`
                            (`games_services_id`, `gss_stat_id`, `created_at`)
                            VALUES ' . join(', ', $statements_bind);

                        DB::connection('gss_stat_bind')->insert($query, $values_bind);
                    }
                }
            }
        }

        return true;
    }

    protected function prepareShardData($streams)
    {
        $data = [];

        foreach ($streams as $key => $stream) {
            if ($id = $this->getId()) {
                $shard = $this->getShard($id);

                if (!empty($shard) || strlen($shard) > 0) {
                    if (!isset($data[$shard])) {
                        $data[$shard] = [];
                    }

                    $data[$shard][] = array_merge($stream, ['db_id' => $id]);
                }

                unset($streams[$key]);
            }
        }
        unset($streams);

        return $data;
    }

    protected function getId()
    {
        return $this->increment();
    }

    protected function getShard($id)
    {
        return $id % 10;
    }

    protected function increment()
    {
        $query = "UPDATE games_streams_stat_sequence SET `id` = LAST_INSERT_ID(`id` + :counter)";

        $result = DB::connection($this->game_stream_stat->getConnectionName())->update($query, [':counter' => 1]);

        if ($result) {
            $result = (int)array_get(
                DB::connection($this->game_stream_stat->getConnectionName())
                    ->selectOne('SELECT LAST_INSERT_ID() `id`'),
                'id',
                0
            );
        } else {
            $result = false;
        }

        return $result;
    }
}