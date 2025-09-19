<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    use HasFactory;

    protected $table = 'news';
    protected $primaryKey = 'id';
    public $timestamps = true;
    protected $connection = 'mysql';

    protected $attributes = [
        'meta_title' => null,
        'slug'  => null,
        'meta_description'  => null,
        'description'  => null,
        'content'  => null,
        'author'  => null,
        'status'  => 1,
        'thumbnail'  => null,
    ];

    protected $fillable = [
        'title',
        'meta_title',
        'slug',
        'meta_description',
        'description',
        'content',
        'author',
        'status',
        'thumbnail',
    ];
}
