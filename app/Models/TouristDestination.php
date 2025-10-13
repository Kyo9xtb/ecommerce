<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TouristDestination extends Model
{
    use HasFactory;

    protected $table = 'tourist_destinations';
    protected $primaryKey = 'id';
    public $timestamps = true;
    protected $connection = 'mysql';

    protected $attributes = [
        'status' => 1,
        'tour_group' => 1,
        'thumbnail' => null,
    ];

    protected $fillable = [
        'place_name',
        'meta_title',
        'slug',
        'description',
        'details',
        'tour_group',
        'area',
        'thumbnail',
        'status',
    ];

    public function images()
    {
        return $this->hasMany(TouristDestinationImage::class, 'tourist_destination_id', 'id');
    }
}
