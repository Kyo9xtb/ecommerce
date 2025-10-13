<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TouristDestinationImage extends Model
{
    use HasFactory;

    protected $table = 'tourist_destination_images';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $connection = 'mysql';

    protected $attributes = [
        'image' => null,
    ];
    protected $fillable = [
        'tourist_destination_id',
        'image',
    ];
}
