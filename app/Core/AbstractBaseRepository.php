<?php

namespace App\Core;

use Illuminate\Database\Eloquent\Model;

abstract class AbstractBaseRepository implements AbstractBaseInterface
{
    protected $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }
    
}
