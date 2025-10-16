<?php

namespace App\Repositories\Employee;

use App\Core\AbstractBaseRepository;
use App\Models\Employee;
use App\Trait\HasModelCache;
use Illuminate\Support\Facades\Hash;

class EmployeeRepository extends AbstractBaseRepository implements EmployeeInterface
{
    use HasModelCache;

    public function __construct(Employee $model)
    {
        parent::__construct($model);
    }

    public function getAllEmployee()
    {
        $keyCache = ALL_EMPLOYEES;
        $cachedData = $this->getCacheKey($keyCache);

        if ($cachedData) {
            return $cachedData;
        }

        $result = $this->model->get();

        $this->setCacheKey($keyCache, $result);

        return $result;
    }

    public function findEmployeeById($id)
    {
        $keyCache = EMPLOYEES_ID . $id;
        $cachedData = $this->getCacheKey($keyCache);

        if ($cachedData) {
            return $cachedData;
        }

        $result = $this->find($id);

        $this->setCacheKey($keyCache, $result);

        return $result;
    }

    public function findEmployeeByCode($code)
    {
        return $this->model->where('employee_code', $code)->first();
    }

    public function findEmployeeByEmail($email)
    {
        return $this->model->where('email', $email)->first();
    }

    public function createEmployee($data)
    {
        $res = $this->create($data);
        $this->clearCacheModel();
        return $res;
    }

    public function updateEmployee($id, $data)
    {
        $res = $this->update($id, $data);
        $this->clearCacheModel();
        return $res;
    }

    public function deleteEmployee($id)
    {
        $res = $this->delete($id);
        $this->clearCacheModel();
        return $res;
    }

    private function clearCacheModel()
    {
        $this->clearCache([
            'direct' => [ALL_EMPLOYEES],
            'patterns' => [EMPLOYEES_ID . '*'],
        ]);
    }

    public function loginEmployee($email, $password)
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
