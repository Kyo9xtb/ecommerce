<?php

namespace App\Repositories\Customer;


interface CustomerInterface
{
    public function getAllCustomer();
    public function findCustomerById($id);
    public function findCustomerByCode($code);
    public function findCustomerByEmail($email);
    public function createCustomer($data);
    public function updateCustomer($id, $data);
    public function deleteCustomer($id);
    public function loginCustomer($email, $password);
}
