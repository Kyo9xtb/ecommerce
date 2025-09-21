<?php

namespace App\Repositories\Cart;

use App\Core\AbstractBaseRepository;
use App\Models\Cart;

class CartRepository extends AbstractBaseRepository implements CartInterface
{
    public function __construct(Cart $model)
    {
        parent::__construct($model);
    }


    public function getCartByUserId(int $id)
    {
        $result = $this->model->where('user_id', $id)->get();
        return $result;
    }

    public function createCart($data)
    {
        return $result = $this->create($data);
    }

    public function updateCart($id, array $data)
    {
        return $result = $this->update($id, $data);
    }

    public function deleteCart($id)
    {
        return $result = $this->delete($id);
    }

    public function deleteAllCartByUserId($id)
    {
        return $result = $this->model->where('user_id', $id)->delete();
    }
}
