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

        $games_streams_stat_binds = $this->findGamesStreamsStatBindsByGamesServices(
            $games_services,
            $period_start,
            $period_end,
            $limit,
            $offset
        );

        $data = $this->normalizeGameStatBinds($games_streams_stat_binds);

        $result = [];

        foreach ($data as $shard => &$shard_data) {
            foreach ($shard_data as $games_streams_stat_suffix => &$games_streams_stat_binds) {
                $table_name = sprintf('games_streams_stat_%s', $games_streams_stat_suffix);

                $query = "
                    SELECT
                      `id`,
                      `external_stream_id`
                    FROM
                      `$table_name` 
                    WHERE
                      `id` IN(" . join(', ', array_map('intval', array_column($games_streams_stat_binds, 'id'))) . ")";

                $res = DB::connection("gss_stat_$shard")->select($query);

                foreach ($res as $row) {
                    $id = array_get($row, 'id');

                    if (isset($games_streams_stat_binds[$id])) {
                        $result[$id] = array_merge(
                            $games_streams_stat_binds[$id],
                            ['stream_id' => array_get($row, 'external_stream_id')]
                        );

                        unset($games_streams_stat_binds[$id]);
                    }
                }
            }

            unset($games_streams_stat_binds, $data[$shard]);
        }

        unset($shard_data, $data);

        return collect($result)->map(function ($arg) {
            return is_array($arg) ? collect($arg) : $arg;
        });
    }

    public function findViewersForGamesServices(
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

        $games_streams_stat_binds = $this->findGamesStreamsStatBindsByGamesServices(
            $games_services,
            $period_start,
            $period_end,
            $limit,
            $offset
        );

        $data = $this->normalizeGameStatBinds($games_streams_stat_binds);

        $result = [];

        foreach ($data as $shard => &$shard_data) {
            foreach ($shard_data as $games_streams_stat_suffix => &$games_streams_stat_binds) {
                $table_name = sprintf('games_streams_stat_%s', $games_streams_stat_suffix);

                $query = "
                    SELECT
                      `id`,
                      `external_viewers_count`
                    FROM
                      `$table_name` 
                    WHERE
                      `id` IN(" . join(', ', array_map('intval', array_column($games_streams_stat_binds, 'id'))) . ")";

                $res = DB::connection("gss_stat_$shard")->select($query);

                foreach ($res as $row) {
                    $id = array_get($row, 'id');

                    if (isset($games_streams_stat_binds[$id])) {
                        $result[$id] = array_merge(
                            $games_streams_stat_binds[$id],
                            ['stream_id' => array_get($row, 'external_stream_id')]
                        );

                        unset($games_streams_stat_binds[$id]);
                    }
                }
            }

            unset($games_streams_stat_binds, $data[$shard]);
        }

        unset($shard_data, $data);

        return collect($result)->map(function ($arg) {
            return is_array($arg) ? collect($arg) : $arg;
        });
    }

    protected function normalizeGameStatBinds($games_streams_stat_binds)
    {
        $data = [];

        foreach ($games_streams_stat_binds as $key => $games_streams_stat_bind) {
            $games_streams_stat_id = array_get($games_streams_stat_bind, 'id');
            $games_streams_stat_suffix = array_get($games_streams_stat_bind, 'created_at');

            if (empty($games_streams_stat_id) || empty($games_streams_stat_suffix)) {
                continue;
            }

            $shard = $this->getShard($games_streams_stat_id);

            if (!isset($data[$shard]))
                $data[$shard] = [];

            $games_streams_stat_suffix = date('Y_m', strtotime($games_streams_stat_suffix));

            if (!isset($data[$shard][$games_streams_stat_suffix]))
                $data[$shard][$games_streams_stat_suffix] = [];

            $data[$shard][$games_streams_stat_suffix][$games_streams_stat_id] = $games_streams_stat_bind;
            unset($games_streams_stat_binds[$key]);
        }

        unset($games_streams_ids);

        return $data;
    }

    protected function findGssStatBindTables($period_start, $period_end)
    {
        $period_from = new \DateTime(date('Y-m-01', strtotime($period_start)));
        $period_to = new \DateTime(date('Y-m-t', strtotime($period_end)));

        $period = new \DatePeriod($period_from, new \DateInterval('P1M'), $period_to);

        $values = [];

        foreach($period as $date) {
            $suffix = $date->format('Y_m');
            $values[sprintf(':table_%s', $suffix)] = sprintf('gss_stat_bind_%s', $suffix);
        }

        if ($values) {
            $connection = DB::connection('gss_stat_bind');
            $result = $connection
                ->select("
                    SHOW TABLES FROM {$connection->getDatabaseName()} 
                    WHERE
                        `Tables_in_{$connection->getDatabaseName()}` LIKE " .  join(" OR  `Tables_in_{$connection->getDatabaseName()}` LIKE ", array_keys($values)). " 
                    ", $values);

            if ($result) {
                foreach ($result as &$row) {
                    $row = current($row);
                }

                unset($row);
                sort($result);
            } else {
                $result = [];
            }
        } else {
            $result = [];
        }

        return $result;
    }

    protected function findGamesStreamsStatBindsByGamesServices($games_services, $period_start, $period_end, $limit, $offset)
    {
        if (empty($limit)) {
            $result = [];
        } else {
            $games_services_ids = [];

            foreach ($games_services as $game_service) {
                $games_services_ids[$game_service->id] = 1;
            }

            if ($games_services_ids) {
                $result = [];

                $limit_original = $limit;

                $period_start = static::periodToDateTime($period_start);
                $period_end = static::periodToDateTime($period_end);

                foreach ($this->findGssStatBindTables($period_start, $period_end) as $table_name) {
                    $query = "
                        SELECT
                          `gss_stat_id` `id`,
                          `games_services_id`,
                          `created_at`
                        FROM
                          `$table_name`
                        WHERE
                          `games_services_id` IN(" . join(', ', array_map('intval', array_keys($games_services_ids))) . ")
                          AND `created_at` BETWEEN :period_start AND :period_end
                        ORDER BY
                            `created_at`
                        LIMIT :offset, :limit";

                    $res = DB::connection('gss_stat_bind')->select($query, [
                        ':period_start' => $period_start,
                        ':period_end' => $period_end,
                        ':offset' => $offset,
                        ':limit' => $limit,
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

                    $limit -= $cnt;
                    $limit = max($limit, 0);
                }

                $result = array_slice($result, 0, $limit_original);
            } else {
                $result = [];
            }
        }

        return $result;
    }

    protected static function periodToDateTime($period)
    {
        $period = trim($period);
        return date('Y-m-d H:i:s', is_int($period) ? $period : strtotime($period));
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