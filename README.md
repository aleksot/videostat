- Videostat\Services\GamesStreamsCollectorService отвечает за сборку статистики с удалённых сервисов (Twitch)
- Videostat\Database\Repositories\Eloquent содержит в себе реализацию репозиториев которые общаются с БД (привязываются через DI)
- Videostat\Services\Adapters\Service содержит в себе прослойки для общения со сторонними сервисами (Twitch)
- Videostat\Components содержит фасады компоннтов


Тестами контроллеры покрыть не успел

Для работы необходимо завести 11 БД (см. конфиг) 
И выполнить следующие команды:

php artisan migrate:install

php artisan migrate

php artisan passport:install

Команда для сбора статистики: php artisan gss:collect

Авторизация происходит по http://example.com/oauth/token и заточена на сервер-сервер запросы

API:

http://example.com/api/stream?games_ids=1,2&period_start=2018-08-15&period_end=2018-08-17

http://example.com/api/stream/viewers?games_ids=1,2&period_start=2018-08-15&period_end=2018-08-17