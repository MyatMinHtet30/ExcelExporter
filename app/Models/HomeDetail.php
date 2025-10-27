<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomeDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'status',
        'no',
        'home_id',
        'category_name',
        'item_name',
        'amount',
        'unit',
        'mc_price',
        'lc_price',
        'material_total',
        'labor_total',
        'grand_total',
    ];

    // Relationship
    public function home()
    {
        return $this->belongsTo(Home::class);
    }
}
