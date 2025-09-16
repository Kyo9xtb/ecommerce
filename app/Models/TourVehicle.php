<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TourVehicle extends Model
{
    use HasFactory;

    protected $table = 'tour_vehicle';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $connection = 'mysql';

    protected $attributes = [
        'code_vehicle' => 0,
    ];
    protected $fillable = [
        'tour_id',
        'code_vehicle',
    ];
}
