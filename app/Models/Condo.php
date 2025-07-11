<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Condo extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_name',
        'address',
        'job_name',
        'quotation_no',
        'date',
        'payment_term',
        'credit',
        'status',
    ];
}
