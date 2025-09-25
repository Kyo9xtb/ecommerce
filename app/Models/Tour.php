<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tour extends Model
{
    use HasFactory;

    protected $table = 'tours';
    protected $primaryKey = 'id';
    public $timestamps = true;
    protected $connection = 'mysql';

    protected $attributes = [
        'status' => 1,
        'price' => 0,
        'sale' => 0,
        'tour_group' => 1,
        'thumbnail' => null,
    ];

    protected $fillable = [
        'tour_name',
        'slug',
        'price',
        'sale',
        'trip',
        'time',
        'departure_schedule',
        'status',
        'area',
        'tour_group',
        'thumbnail'
    ];

    public function detail()
    {
        return $this->hasOne(TourDetail::class, 'tour_id', 'id');
    }

    public function images()
    {
        return $this->hasMany(TourImage::class, 'tour_id', 'id');
    }

    public function vehicles()
    {
        return $this->hasMany(TourVehicle::class, 'tour_id', 'id');
    }
    public function guests()
    {
        return $this->hasMany(TourGuest::class, 'tour_id', 'id');
    }
}
