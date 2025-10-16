<?php

namespace App\Repositories\Employee;


interface EmployeeInterface
{
    public function getAllEmployee();
    public function findEmployeeById($id);
    public function findEmployeeByCode($code);
    public function findEmployeeByEmail($email);
    public function createEmployee($data);
    public function updateEmployee($id, $data);
    public function deleteEmployee($id);
    public function loginEmployee($email, $password);
}
