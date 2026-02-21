<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    use HasFactory;

    protected $fillable = [
        'image_path',
        'home_id',
        'condo_id',
        'status',
    ];

    /**
     * Relationship: Image belongs to a Home
     */
    public function home()
    {
        return $this->belongsTo(Home::class, 'home_id');
    }

    /**
     * Relationship: Image belongs to a Condo
     */
    public function condo()
    {
        return $this->belongsTo(Condo::class, 'condo_id');
    }
}
