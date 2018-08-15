<?php

namespace Videostat\Console\Commands;

use Illuminate\Console\Command;
use Videostat\Components\GamesStreamsCollector;

class CollectCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gss:collect';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Collect games streams statistic';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        ignore_user_abort(true);
        set_time_limit(0);

        GamesStreamsCollector::run();
    }
}
