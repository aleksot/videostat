<?php

namespace Videostat\Contracts\Database\Repositories;

interface GameServiceQueueRepository
{
    public function findLocked($lock_value, $limit);
    public function acquireLock($lock_value, $limit);
    public function releaseLock($lock_value, $limit);

}