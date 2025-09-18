<?php

namespace App\Repositories\ContactCustomer;


interface ContactCustomerInterface
{
    public function fetchAll();
    public function findContactById($id);
    public function createContact($data);
    public function updateContact($id, $data);
    public function deleteContact($id);
}
