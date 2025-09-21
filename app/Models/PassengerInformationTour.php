<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PassengerInformationTour extends Model
{
    use HasFactory;

    protected $table = 'passenger_information_tours';
    protected $primaryKey = 'id';
    public $timestamps = true;
    protected $connection = 'mysql';

    protected $attributes = [
        'booking_id' => null,
        'tour_id'  => null,
        'customer'  => 1,
        'full_name'  => null,
        'birthday'  => null,
        'gender'  => 1,
        'card_id'  => null,
    ];

    protected $fillable = [
        'booking_id',
        'tour_id',
        'customer',
        'full_name',
        'birthday',
        'gender',
        'card_id',
    ];
}
