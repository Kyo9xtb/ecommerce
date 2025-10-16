<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;
    protected $fillable = [
        'customer_code',
        'full_name',
        'email',
        'card_id',
        'birth_date',
        'address',
        'phone',
        'gender',
        'status',
        'password',
    ];

    protected $attributes = [
        'gender' => 0,
        'status' => 1,
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'password' => 'hashed',
    ];

    protected $connection = 'mysql';
    protected $table = 'customers';
    protected $primaryKey = 'id';
    public $timestamps = true;
}
