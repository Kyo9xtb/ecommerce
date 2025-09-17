<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingTour extends Model
{
    use HasFactory;

    protected $table = 'booking_tours';
    protected $primaryKey = 'id';
    public $timestamps = true;
    protected $connection = 'mysql';

    protected $attributes = [
        'user_id' => null,
        'total_price' => 0,
        'deposit' => 0,
        'status' => 1,
        'currency' => 'VND',
        'payment_method' => null,
    ];

    protected $fillable = [
        'user_id',
        'full_name',
        'email',
        'phone',
        'address',
        'note',
        'status',
        'total_price',
        'deposit',
        'currency',
        'payment_method',
    ];

    public function details()
    {
        return $this->hasMany(BookingTourDetails::class, 'booking_id', 'id');
    }
}
