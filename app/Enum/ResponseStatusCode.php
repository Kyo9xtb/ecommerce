<?php

namespace App\Enum;

enum ResponseStatusCode: int
{
    const NONE_ERR = 0;
    const SUCCESS = 200;
    const PARAMS_INVALID = 1002;
}
