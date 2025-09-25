<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TourGuest extends Model
{
    use HasFactory;

    protected $table = 'tour_guest_type';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $connection = 'mysql';

    protected $attributes = [
        'guest_code' => 0,
    ];
    protected $fillable = [
        'tour_id',
        'guest_code',
    ];
}
