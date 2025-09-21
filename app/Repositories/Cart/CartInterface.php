<?php

namespace App\Repositories\Cart;


interface CartInterface
{
    public function getCartByUserId(int $id);
    public function createCart($data);
    public function updateCart($id, array $data);
    public function deleteCart($id);
    public function deleteAllCartByUserId($id);
}
