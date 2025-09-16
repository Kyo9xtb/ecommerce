<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TourRequire extends Model
{
    use HasFactory;

    protected $table = 'tour_requests';
    protected $primaryKey = 'id';
    public $timestamps = true;
    protected $connection = 'mysql';

    protected $attributes = [
        'feedback' => 1,
    ];
    protected $fillable = [
        'full_name',
        'nationality',
        'email',
        'phone',
        'expected_destination',
        'departure_date',
        'end_date',
        'expected_month',
        'expected_year',
        'number_days',
        'vehicle',
        'adult',
        'children',
        'baby',
        'number_rooms',
        'hotel_standards',
        'note',
        'feedback',
        'status'
    ];
}
