<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Condo extends Model
{
    protected $fillable = [
        'customer_name','address','job_name',
        'quotation_number','quotation_date',
        'payment_term','credits',
        'total','vat','grand_total','status',
    ];

    protected $casts = [
        'quotation_date' => 'date',
        'total' => 'decimal:2',
        'vat' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'status' => 'boolean',
    ];

    public function details()
    {
        return $this->hasMany(CondoDetail::class);
    }
}
