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
        return $this->queryWithRelations()->where('user_id', $id)->first();
    }

    public function createCart($data)
    {
        // dd($data);
        $cart = $this->model->create(
            array_intersect_key($data, array_flip([
                'user_id',
                'total_amount',
                'status',
            ]))
        );

        if (!empty($data['details']) && is_array($data['details'])) {
            $details = array_map(function ($detail) {
                return [
                    'tour_id'        => $detail['tour_id'] ?? null,
                    'quantity'       => $detail['quantity'] ?? 0,
                    'unit_price'     => $detail['unit_price'] ?? 0,
                    'guest_id'       => $detail['guest_id'] ?? 0,
                    'departure_date' => $detail['departure_date'] ?? null,
                ];
            }, $data['details']);

            $cart->details()->createMany($details);
        }

        return $cart->fresh();
    }

    public function updateCart($id, array $data)
    {
        $cart = $this->find($id);
        if (!$cart) return null;

        $cart->update(
            array_intersect_key($data, array_flip([
                'user_id',
                'total_amount',
                'status',
            ]))
        );

        $cart->details()->delete();

        if (!empty($data['details']) && is_array($data['details'])) {
            $details = array_map(function ($detail) {
                return [
                    'tour_id'        => $detail['tour_id'] ?? null,
                    'quantity'       => $detail['quantity'] ?? 0,
                    'unit_price'          => $detail['unit_price'] ?? 0,
                    'guest_id'          => $detail['guest_id'] ?? 0,
                    'departure_date'          => $detail['departure_date'],
                ];
            }, $data['details']);

            $cart->details()->createMany($details);
        }

        return $cart->fresh();
    }

    public function deleteCart($id)
    {
        return $result = $this->delete($id);
    }

    public function deleteAllCartByUserId($id)
    {
        return $result = $this->model->where('user_id', $id)->delete();
    }

    private function queryWithRelations()
    {
        return $this->model->with(['details']);
    }
}
