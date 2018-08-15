<?php

namespace Videostat\Providers;

use Illuminate\Support\ServiceProvider;

use TwitchApi\TwitchApi;

use Videostat\Components\StreamApi;
use Videostat\Components\GamesStreamsCollector;
use Videostat\Components\ServiceApi;

use Videostat\Services\Adapters\Service\Twitch;
use Videostat\Services\ServiceApiService;
use Videostat\Services\Api\StreamService;

use Videostat\Contracts\Database\Models\Game as GameContract;
use Videostat\Contracts\Database\Repositories\GameRepository as GameRepositoryContract;
use Videostat\Database\Models\Eloquent\Game;
use Videostat\Database\Repositories\Eloquent\GameRepository;

use Videostat\Contracts\Database\Models\Service as ServiceContract;
use Videostat\Contracts\Database\Repositories\ServiceRepository as ServiceRepositoryContract;
use Videostat\Database\Models\Eloquent\Service;
use Videostat\Database\Repositories\Eloquent\ServiceRepository;

use Videostat\Contracts\Database\Models\GameService as GameServiceContract;
use Videostat\Contracts\Database\Repositories\GameServiceRepository as GameServiceRepositoryContract;
use Videostat\Database\Models\Eloquent\GameService;
use Videostat\Database\Repositories\Eloquent\GameServiceRepository;

use Videostat\Contracts\Database\Models\GameServiceQueue as GameServiceQueueContract;
use Videostat\Contracts\Database\Repositories\GameServiceQueueRepository as GameServiceQueueRepositoryContract;
use Videostat\Database\Models\Eloquent\GameServiceQueue;
use Videostat\Database\Repositories\Eloquent\GameServiceQueueRepository;

use Videostat\Contracts\Database\Models\GameStreamStat as GameStreamStatContract;
use Videostat\Contracts\Database\Repositories\GameStreamStatRepository as GameStreamStatRepositoryContract;
use Videostat\Database\Models\Eloquent\GameStreamStat;
use Videostat\Database\Repositories\Eloquent\GameStreamStatRepository;

use Videostat\Services\GamesStreamsCollectorService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(GameContract::class, function() {
            return new Game;
        });

        $this->app->bind(GameRepositoryContract::class, function($app) {
            return new GameRepository($app->make(GameContract::class));
        });

        $this->app->bind(ServiceContract::class, function() {
            return new Service;
        });

        $this->app->bind(ServiceRepositoryContract::class, function($app) {
            return new ServiceRepository($app->make(ServiceContract::class));
        });

        $this->app->bind(GameServiceContract::class, function() {
            return new GameService;
        });

        $this->app->bind(GameServiceRepositoryContract::class, function($app) {
            return new GameServiceRepository($app->make(GameServiceContract::class));
        });

        $this->app->bind(GameServiceQueueContract::class, function() {
            return new GameServiceQueue;
        });

        $this->app->bind(GameServiceQueueRepositoryContract::class, function($app) {
            return new GameServiceQueueRepository($app->make(GameServiceQueueContract::class));
        });

        $this->app->bind(GameStreamStatContract::class, function() {
            return new GameStreamStat;
        });

        $this->app->bind(GameStreamStatRepositoryContract::class, function($app) {
            return new GameStreamStatRepository($app->make(GameStreamStatContract::class));
        });

        $this->app->bind(GamesStreamsCollector::FACADE_ACCESSOR, function($app) {
            return new GamesStreamsCollectorService(
                $app->make(GameServiceQueueRepositoryContract::class),
                $app->make(GameServiceRepositoryContract::class),
                $app->make(ServiceRepositoryContract::class),
                $app->make(GameStreamStatRepositoryContract::class)
            );
        });

        $this->app->bind(ServiceApi::FACADE_ACCESSOR, function() {
            return new ServiceApiService;
        });

        $this->app->bind(StreamApi::FACADE_ACCESSOR, function($app) {
            return new StreamService($app->make(GameStreamStatRepositoryContract::class));
        });

        $this->app->bind(TwitchApi::class, function() {
            return new TwitchApi([
                'client_id' => env('GSS_API_SERVICE_TWITCH_API_OPTION_CLIENT_ID'),
            ]);
        });

        $this->app->bind(Twitch::class, function($app) {
            return new Twitch($app->make(TwitchApi::class));
        });
    }
}
