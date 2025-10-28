<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $table = 'cart';
    protected $primaryKey = 'id';
    public $timestamps = true;
    protected $connection = 'mysql';

    protected $attributes = [
        'total_amount' => 0,
        'status' => 1,
    ];

    protected $fillable = [
        'user_id',
        'total_amount',
        'status',
    ];

    public function details()
    {
        return $this->hasMany(CartItem::class, 'cart_id', 'id');
    }
}
