<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TourRequestDetail extends Model
{
    use HasFactory;

    protected $table = 'tour_request_details';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $connection = 'mysql';

    protected $attributes = [
        'price' => 0,
        'tour_program' => null,
        'tour_policy' => null,
        'terms_conditions' => null,
    ];

    protected $fillable = [
        'tour_id',
        'price',
        'tour_program',
        'tour_policy',
        'terms_conditions',
    ];
}
