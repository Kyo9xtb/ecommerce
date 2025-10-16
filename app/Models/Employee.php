<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;
    protected $fillable = [
        'employee_code',
        'full_name',
        'email',
        'birth_date',
        'address',
        'phone',
        'gender',
        'status',
        'position',
        'department',
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
    protected $table = 'employees';
    protected $primaryKey = 'id';
    public $timestamps = true;
}
