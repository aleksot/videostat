<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class VideostatInstall extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection('videostat')->statement("
            CREATE TABLE IF NOT EXISTS `games` (
              `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
              `title` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
              `code` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL,
              `is_active` tinyint(1) unsigned NOT NULL DEFAULT '0',
              `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
              `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
              PRIMARY KEY (`id`),
              UNIQUE KEY `code` (`code`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        DB::connection('videostat')->statement("
            CREATE TABLE IF NOT EXISTS `services` (
              `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
              `title` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL,
              `code` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL,
              `is_active` tinyint(1) unsigned NOT NULL DEFAULT '0',
              `created_ad` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
              `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
              PRIMARY KEY (`id`),
              UNIQUE KEY `code` (`code`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        DB::connection('videostat')->statement("
            CREATE TABLE IF NOT EXISTS `games_services` (
              `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
              `game_id` int(10) unsigned NOT NULL,
              `service_id` int(10) unsigned NOT NULL,
              `is_active` tinyint(1) unsigned NOT NULL DEFAULT '0',
              `external_game_code` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
              `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
              `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
              PRIMARY KEY (`id`),
              UNIQUE KEY `game_id` (`game_id`,`service_id`),
              KEY `service_id` (`service_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        DB::connection('videostat')->statement("
            CREATE TABLE IF NOT EXISTS `games_services_queue` (
              `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
              `games_services_id` int(10) unsigned NOT NULL,
              `lock_id` int(10) unsigned NOT NULL DEFAULT '0',
              `lock_value` int(11) NOT NULL DEFAULT '0',
              `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
              `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
              PRIMARY KEY (`id`),
              UNIQUE KEY `games_services_id` (`games_services_id`,`lock_value`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        DB::connection('videostat')->statement("
            CREATE TABLE IF NOT EXISTS `games_streams_stat_sequence` (
              `id` bigint(20) unsigned NOT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        DB::connection('videostat')->insert("INSERT INTO `games_streams_stat_sequence` (`id`) VALUES (0)");

        DB::connection('gss_stat_bind')->statement("
            CREATE TABLE IF NOT EXISTS `gss_stat_bind` (
              `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
              `games_services_id` int(10) unsigned NOT NULL,
              `gss_stat_id` bigint(20) unsigned NOT NULL,
              `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
              PRIMARY KEY (`id`),
              KEY `games_services_id` (`games_services_id`,`created_at`,`gss_stat_id`)
            ) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        for ($i = 0; $i < 10; $i++) {
            DB::connection("gss_stat_$i")->statement("
                CREATE TABLE IF NOT EXISTS `games_streams_stat` (
                  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                  `external_stream_id` bigint(20) unsigned NOT NULL,
                  `external_viewers_count` int(10) unsigned NOT NULL,
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::connection('videostat')->statement("DROP TABLE IF EXISTS `games`");

        DB::connection('videostat')->statement("DROP TABLE IF EXISTS `services`");

        DB::connection('videostat')->statement("DROP TABLE IF EXISTS `games_services`");

        DB::connection('videostat')->statement("DROP TABLE IF EXISTS `games_services_queue`");

        DB::connection('videostat')->statement("DROP TABLE IF EXISTS `games_streams_stat_sequence`");

        DB::connection('gss_stat_bind')->statement("DROP TABLE IF EXISTS `gss_stat_bind`");

        for ($i = 0; $i < 10; $i++) {
            DB::connection("gss_stat_$i")->statement("DROP TABLE IF EXISTS `games_streams_stat`");
        }
    }
}
