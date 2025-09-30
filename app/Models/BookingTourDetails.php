<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingTourDetails extends Model
{
    use HasFactory;

    protected $table = 'booking_details';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $connection = 'mysql';

    protected $attributes = [
        'price' => 0,
        'quantity' => 0,
        'departure_date' => null,
    ];

    protected $fillable = [
        'booking_id',
        'tour_id',
        'guest_id',
        'price',
        'quantity',
        'departure_date',
    ];
    public function tour()
    {
        return $this->belongsTo(Tour::class, 'tour_id', 'id');
    }
}
