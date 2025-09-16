<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TourImage extends Model
{
    use HasFactory;

    protected $table = 'tour_image';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $connection = 'mysql';

    protected $attributes = [
        'thumbnail' => null,
        'image' => null,
    ];
    protected $fillable = [
        'tour_id',
        'thumbnail',
        'image',
    ];
}
