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
        'feedback_method' => 1,
        'adult' => 0,
        'children' => 0,
        'baby' => 0,
        'number_days' => 0,
        'number_rooms' => 0,
        'expected_month' => 0,
        'expected_year' => 0,
    ];
    protected $fillable = [
        'tour_code',
        'full_name',
        'nationality',
        'email',
        'phone',
        'tour_dates',
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
        'feedback_method',
        'status'
    ];

    public function detail()
    {
        return $this->hasOne(TourRequestDetail::class, 'tour_id', 'id');
    }
}
