<?php

namespace App\Repositories\User;

use App\Core\AbstractBaseRepository;
use App\Models\User;
use Illuminate\Support\Facades\Redis;

class UserRepository extends AbstractBaseRepository implements UserInterface
{

    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    public function getAllUser()
    {
        $result = $this->model->get();
        if ($result->isEmpty()) {
            return null;
        }
        return $result;
    }

    

    private function clearCache()
    {
        $store = Redis::connection();
        $store->del(ALL_USER);
    }
}
