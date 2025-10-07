<?php

namespace App\Repositories\ContactCustomer;

use App\Core\AbstractBaseRepository;
use App\Models\ContactCustomer;
use App\Trait\HasModelCache;

class ContactCustomerRepository extends AbstractBaseRepository implements ContactCustomerInterface
{
    use HasModelCache;

    public function __construct(ContactCustomer $model)
    {
        parent::__construct($model);
    }

    public function fetchAll()
    {
        $keyCache = ALL_CONTACT;
        $cachedData = $this->getCacheKey($keyCache);

        if ($cachedData) {
            return $cachedData;
        }

        $result = $this->all();

        $this->setCacheKey($keyCache, $result, 604800);

        return $result;
    }

    public function findContactById($id)
    {
        $keyCache = CONTACT_ID . $id;
        $cachedData = $this->getCacheKey($keyCache);

        if ($cachedData) {
            return $cachedData;
        }

        $result = $this->find($id);

        $this->setCacheKey($keyCache, $result, 604800);

        return $result;
    }

    public function findContactByCode($code)
    {
        return $this->model->where('contact_code', $code)->first();
    }

    public function createContact($data)
    {
        $res = $this->create($data);
        $this->clearCacheModel();
        return $res;
    }

    public function updateContact($id, $data)
    {
        $res = $this->update($id, $data);
        $this->clearCacheModel();
        return $res;
    }

    public function deleteContact($id)
    {
        $res = $this->delete($id);
        $this->clearCacheModel();
        return $res;
    }

    private function clearCacheModel()
    {
        $this->clearCache([
            'direct' => [ALL_CONTACT],
            'patterns' => [CONTACT_ID . '*'],
        ]);
    }
}
