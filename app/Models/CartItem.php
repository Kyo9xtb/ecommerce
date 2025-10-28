<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    use HasFactory;
    protected $table = 'cart_items';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $connection = 'mysql';

    protected $attributes = [
        'unit_price' => 0,
        'quantity' => 1,
    ];

    protected $fillable = [
        'cart_id',
        'tour_id',
        'guest_id',
        'quantity',
        'unit_price',
        'departure_date'
    ];

    public function tour()
    {
        return $this->belongsTo(Tour::class, 'tour_id', 'id');
    }
}
