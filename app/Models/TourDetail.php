<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TourDetail extends Model
{
    use HasFactory;

    protected $table = 'tour_description';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $connection = 'mysql';

    protected $attributes = [
        'tour_summary' => null,
        'tour_program' => null,
        'tour_policy' => null,
        'terms_conditions' => null,
    ];

    protected $fillable = [
        'tour_id',
        'tour_summary',
        'tour_program',
        'tour_policy',
        'terms_conditions',
    ];
}
