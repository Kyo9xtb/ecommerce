<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $table = 'user_cart';
    protected $primaryKey = 'id';
    public $timestamps = true;
    protected $connection = 'mysql';

    protected $attributes = [
        'customer' => 1,
        'quantity' => 0,
        'price' => 0,
    ];

    protected $fillable = [
        'user_id',
        'tour_id',
        'customer',
        'quantity',
        'price'
    ];
}
