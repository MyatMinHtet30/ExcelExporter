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
        'amount',
        'unit',
        'mc_price',
        'lc_price',
    ];

    // Relationship to Home
    public function home()
    {
        return $this->belongsTo(Home::class, 'home_id');
    }
}
