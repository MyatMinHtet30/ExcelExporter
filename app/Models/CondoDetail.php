<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CondoDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'condo_id',
        'no',
        'details',
        'amount',
        'unit',
        'material_cost',
        'labor_cost',
        'price_per_unit_total',
        'sub_total',
        'total',
        'tax_7_percent',
        'total_price',
        'status'
    ];
}
