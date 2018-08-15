<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Database Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the database connections below you wish
    | to use as your default connection for all database work. Of course
    | you may use many connections at once using the Database library.
    |
    */

    'default' => env('DB_CONNECTION', 'mysql'),

    /*
    |--------------------------------------------------------------------------
    | Database Connections
    |--------------------------------------------------------------------------
    |
    | Here are each of the database connections setup for your application.
    | Of course, examples of configuring each database platform that is
    | supported by Laravel is shown below to make development simple.
    |
    |
    | All database work in Laravel is done through the PHP PDO facilities
    | so make sure you have the driver for your particular database of
    | choice installed on your machine before you begin development.
    |
    */

    'connections' => [

        'sqlite' => [
            'driver' => 'sqlite',
            'database' => env('DB_DATABASE', database_path('database.sqlite')),
            'prefix' => '',
        ],

        'videostat' => [
            'driver' => 'mysql',
            'host' => env('DB_HOST_GSS', '127.0.0.1'),
            'port' => env('DB_PORT_GSS', '3306'),
            'database' => env('DB_DATABASE_GSS', 'forge'),
            'username' => env('DB_USERNAME_GSS', 'forge'),
            'password' => env('DB_PASSWORD_GSS', ''),
            'unix_socket' => env('DB_SOCKET_GSS', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
        ],

        'gss_stat_bind' => [
            'driver' => 'mysql',
            'host' => env('DB_HOST_GSS_STAT_BIND', '127.0.0.1'),
            'port' => env('DB_PORT_GSS_STAT_BIND', '3306'),
            'database' => env('DB_DATABASE_GSS_STAT_BIND', 'forge'),
            'username' => env('DB_USERNAME_GSS_STAT_BIND', 'forge'),
            'password' => env('DB_PASSWORD_GSS_STAT_BIND', ''),
            'unix_socket' => env('DB_SOCKET_GSS_STAT_BIND', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
        ],

        'gss_stat_0' => [
            'driver' => 'mysql',
            'host' => env('DB_HOST_GSS_STAT_0', '127.0.0.1'),
            'port' => env('DB_PORT_GSS_STAT_0', '3306'),
            'database' => env('DB_DATABASE_GSS_STAT_0', 'forge'),
            'username' => env('DB_USERNAME_GSS_STAT_0', 'forge'),
            'password' => env('DB_PASSWORD_GSS_STAT_0', ''),
            'unix_socket' => env('DB_SOCKET_GSS_STAT_0', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
        ],

        'gss_stat_1' => [
            'driver' => 'mysql',
            'host' => env('DB_HOST_GSS_STAT_1', '127.0.0.1'),
            'port' => env('DB_PORT_GSS_STAT_1', '3306'),
            'database' => env('DB_DATABASE_GSS_STAT_1', 'forge'),
            'username' => env('DB_USERNAME_GSS_STAT_1', 'forge'),
            'password' => env('DB_PASSWORD_GSS_STAT_1', ''),
            'unix_socket' => env('DB_SOCKET_GSS_STAT_1', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
        ],

        'gss_stat_2' => [
            'driver' => 'mysql',
            'host' => env('DB_HOST_GSS_STAT_2', '127.0.0.1'),
            'port' => env('DB_PORT_GSS_STAT_2', '3306'),
            'database' => env('DB_DATABASE_GSS_STAT_2', 'forge'),
            'username' => env('DB_USERNAME_GSS_STAT_2', 'forge'),
            'password' => env('DB_PASSWORD_GSS_STAT_2', ''),
            'unix_socket' => env('DB_SOCKET_GSS_STAT_2', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
        ],

        'gss_stat_3' => [
            'driver' => 'mysql',
            'host' => env('DB_HOST_GSS_STAT_3', '127.0.0.1'),
            'port' => env('DB_PORT_GSS_STAT_3', '3306'),
            'database' => env('DB_DATABASE_GSS_STAT_3', 'forge'),
            'username' => env('DB_USERNAME_GSS_STAT_3', 'forge'),
            'password' => env('DB_PASSWORD_GSS_STAT_3', ''),
            'unix_socket' => env('DB_SOCKET_GSS_STAT_3', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
        ],

        'gss_stat_4' => [
            'driver' => 'mysql',
            'host' => env('DB_HOST_GSS_STAT_4', '127.0.0.1'),
            'port' => env('DB_PORT_GSS_STAT_4', '3306'),
            'database' => env('DB_DATABASE_GSS_STAT_4', 'forge'),
            'username' => env('DB_USERNAME_GSS_STAT_4', 'forge'),
            'password' => env('DB_PASSWORD_GSS_STAT_4', ''),
            'unix_socket' => env('DB_SOCKET_GSS_STAT_4', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
        ],

        'gss_stat_5' => [
            'driver' => 'mysql',
            'host' => env('DB_HOST_GSS_STAT_5', '127.0.0.1'),
            'port' => env('DB_PORT_GSS_STAT_5', '3306'),
            'database' => env('DB_DATABASE_GSS_STAT_5', 'forge'),
            'username' => env('DB_USERNAME_GSS_STAT_5', 'forge'),
            'password' => env('DB_PASSWORD_GSS_STAT_5', ''),
            'unix_socket' => env('DB_SOCKET_GSS_STAT_5', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
        ],

        'gss_stat_6' => [
            'driver' => 'mysql',
            'host' => env('DB_HOST_GSS_STAT_6', '127.0.0.1'),
            'port' => env('DB_PORT_GSS_STAT_6', '3306'),
            'database' => env('DB_DATABASE_GSS_STAT_6', 'forge'),
            'username' => env('DB_USERNAME_GSS_STAT_6', 'forge'),
            'password' => env('DB_PASSWORD_GSS_STAT_6', ''),
            'unix_socket' => env('DB_SOCKET_GSS_STAT_6', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
        ],

        'gss_stat_7' => [
            'driver' => 'mysql',
            'host' => env('DB_HOST_GSS_STAT_7', '127.0.0.1'),
            'port' => env('DB_PORT_GSS_STAT_7', '3306'),
            'database' => env('DB_DATABASE_GSS_STAT_7', 'forge'),
            'username' => env('DB_USERNAME_GSS_STAT_7', 'forge'),
            'password' => env('DB_PASSWORD_GSS_STAT_7', ''),
            'unix_socket' => env('DB_SOCKET_GSS_STAT_7', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
        ],

        'gss_stat_8' => [
            'driver' => 'mysql',
            'host' => env('DB_HOST_GSS_STAT_8', '127.0.0.1'),
            'port' => env('DB_PORT_GSS_STAT_8', '3306'),
            'database' => env('DB_DATABASE_GSS_STAT_8', 'forge'),
            'username' => env('DB_USERNAME_GSS_STAT_8', 'forge'),
            'password' => env('DB_PASSWORD_GSS_STAT_8', ''),
            'unix_socket' => env('DB_SOCKET_GSS_STAT_8', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
        ],

        'gss_stat_9' => [
            'driver' => 'mysql',
            'host' => env('DB_HOST_GSS_STAT_9', '127.0.0.1'),
            'port' => env('DB_PORT_GSS_STAT_9', '3306'),
            'database' => env('DB_DATABASE_GSS_STAT_9', 'forge'),
            'username' => env('DB_USERNAME_GSS_STAT_9', 'forge'),
            'password' => env('DB_PASSWORD_GSS_STAT_9', ''),
            'unix_socket' => env('DB_SOCKET_GSS_STAT_9', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
        ],

        'pgsql' => [
            'driver' => 'pgsql',
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '5432'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8',
            'prefix' => '',
            'schema' => 'public',
            'sslmode' => 'prefer',
        ],

        'sqlsrv' => [
            'driver' => 'sqlsrv',
            'host' => env('DB_HOST', 'localhost'),
            'port' => env('DB_PORT', '1433'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8',
            'prefix' => '',
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Migration Repositories Table
    |--------------------------------------------------------------------------
    |
    | This table keeps track of all the migrations that have already run for
    | your application. Using this information, we can determine which of
    | the migrations on disk haven't actually been run in the database.
    |
    */

    'migrations' => 'migrations',

    /*
    |--------------------------------------------------------------------------
    | Redis Databases
    |--------------------------------------------------------------------------
    |
    | Redis is an open source, fast, and advanced key-value store that also
    | provides a richer set of commands than a typical key-value systems
    | such as APC or Memcached. Laravel makes it easy to dig right in.
    |
    */

    'redis' => [

        'client' => 'predis',

        'default' => [
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD', null),
            'port' => env('REDIS_PORT', 6379),
            'database' => 0,
        ],

    ],

];
