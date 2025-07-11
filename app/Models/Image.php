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
        'status',
    ];

    /**
     * Relationship: Image belongs to a Home
     */
    public function home()
    {
        return $this->belongsTo(Home::class, 'home_id');
    }
}
