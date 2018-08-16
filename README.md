- Videostat\Services\GamesStreamsCollectorService отвечает за сборку статистики с удалённых сервисов (Twitch)
- Videostat\Database\Repositories\Eloquent содержит в себе реализацию репозиториев которые общаются с БД (привязываются через DI)
- Videostat\Services\Adapters\Service содержит в себе прослойки для общения со сторонними сервисами (Twitch)
- Videostat\Components содержит фасады компоннтов


Тестами контроллеры покрыть не успел

Для работы необходимо завести 11 БД (см. конфиг) 