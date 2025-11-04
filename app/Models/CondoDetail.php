<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CondoDetail extends Model
{
    protected $fillable = [
        'condo_id','no','details','amount','unit',
        'material_cost','labor_cost','price_per_unit_total','status',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'material_cost' => 'decimal:2',
        'labor_cost' => 'decimal:2',
        'price_per_unit_total' => 'decimal:2',
        'status' => 'boolean',
    ];

    public function condo()
    {
        return $this->belongsTo(Condo::class);
    }
}
