<?php

namespace App\Repositories\ContactCustomer;

use App\Core\AbstractBaseRepository;
use App\Models\ContactCustomer;

class ContactCustomerRepository extends AbstractBaseRepository implements ContactCustomerInterface
{

    public function __construct(ContactCustomer $model)
    {
        parent::__construct($model);
    }

    public function fetchAll()
    {
        return $this->all();
    }
    public function findContactById($id)
    {
        return $this->find($id);
    }

    public function createContact($data)
    {
        return $this->create($data);
    }

    public function updateContact($id, $data)
    {
        return $this->update($id, $data);
    }
    
    public function deleteContact($id)
    {
        return $this->delete($id);
    }
}
