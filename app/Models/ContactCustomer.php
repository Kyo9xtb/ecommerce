<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactCustomer extends Model
{
    use HasFactory;

    protected $table = 'contact_customers';
    protected $primaryKey = 'id';
    public $timestamps = true;
    protected $connection = 'mysql';

    protected $attributes = [
        'full_name' => null,
        'email' => null,
        'phone' => null,
        'contact_content' => null,
        'contact_result' => null,
        'title' => null,
        'status' => 0,
    ];

    protected $fillable = [
        'contact_code',
        'full_name',
        'email',
        'phone',
        'contact_content',
        'contact_result',
        'status',
        'title'
    ];
}
