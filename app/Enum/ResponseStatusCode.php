<?php

namespace App\Enum;

enum ResponseStatusCode: int
{
    const NONE_ERR = 0;
    const SUCCESS = 200;
    const PARAMS_INVALID = 1002;
    const SERVER_ERR = 500;
    const UNAUTHORIZED = 401;
    const FORBIDDEN = 403;
    const NOT_FOUND = 404;
    const TOO_MANY_REQUESTS = 429;
}
