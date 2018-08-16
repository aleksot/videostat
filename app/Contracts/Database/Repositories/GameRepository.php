<?php

namespace Videostat\Contracts\Database\Repositories;

interface GameRepository
{
    public function find($id);
    public function findAll($ids);
}