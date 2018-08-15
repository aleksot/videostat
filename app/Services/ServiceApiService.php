<?php

namespace Videostat\Services;

use Videostat\Contracts\Database\Models\Service;

class ServiceApiService
{
    public static function getInstance(Service $service)
    {
        return resolve(__NAMESPACE__ . '\\' . 'Adapters\Service\\' . ucfirst(array_get($service, 'code')));
    }
}