<?php

namespace Videostat\Database\Repositories\Eloquent;

use Videostat\Contracts\Database\Models\Service;
use Videostat\Contracts\Database\Repositories\ServiceRepository as Contract;

class ServiceRepository implements Contract
{
    protected $service;

    public function __construct(Service $service)
    {
        $this->service = $service;
        $this->service->setConnection('videostat');
    }

    public function find($id)
    {
        $service = $this->service;

        return $service->where($service->getKeyName(), $id)->first();
    }
}