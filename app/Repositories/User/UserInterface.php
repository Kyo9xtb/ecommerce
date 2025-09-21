<?php

namespace App\Repositories\User;


interface UserInterface
{
    public function getAllUser();
    public function findUserActive($id);
}
