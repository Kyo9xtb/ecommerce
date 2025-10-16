<?php

namespace App\Repositories\Customer;

use App\Core\AbstractBaseRepository;
use App\Models\Customer;
use App\Trait\HasModelCache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redis;

class CustomerRepository extends AbstractBaseRepository implements CustomerInterface
{
    use HasModelCache;

    public function __construct(Customer $model)
    {
        parent::__construct($model);
    }

    public function getAllCustomer()
    {
        $keyCache = ALL_CUSTOMER;
        $cachedData = $this->getCacheKey($keyCache);

        if ($cachedData) {
            return $cachedData;
        }

        $result = $this->model->get();

        $this->setCacheKey($keyCache, $result);

        return $result;
    }

    public function findCustomerById($id)
    {
        $keyCache = CUSTOMER_ID . $id;
        $cachedData = $this->getCacheKey($keyCache);

        if ($cachedData) {
            return $cachedData;
        }

        $result = $this->find($id);

        $this->setCacheKey($keyCache, $result);

        return $result;
    }

    public function findCustomerByCode($code)
    {
        return $this->model->where('customer_code', $code)->first();
    }
    
    public function findCustomerByEmail($email)
    {
        return $this->model->where('email', $email)->first();
    }

    public function createCustomer($data)
    {
        $res = $this->create($data);
        $this->clearCacheModel();
        return $res;
    }

    public function updateCustomer($id, $data)
    {
        $res = $this->update($id, $data);
        $this->clearCacheModel();
        return $res;
    }

    public function deleteCustomer($id)
    {
        $res = $this->delete($id);
        $this->clearCacheModel();
        return $res;
    }

    private function clearCacheModel()
    {
        $this->clearCache([
            'direct' => [ALL_CUSTOMER],
            'patterns' => [CUSTOMER_ID . '*'],
        ]);
    }

    public function loginCustomer($email, $password)
    {
        $user = $this->model->where([
            ['email', '=', $email],
            ['status', '=', 1],
        ])->first();

        if (!$user) {
            return null;
        }

        if (!Hash::check($password, $user->password)) {
            return null;
        }

        return $user;
    }
}
