<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Home extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'project_name',
        'dear',
        'date',
        'trooper',
        'house_no',
        'list_name',
        'status',
        'total_price',
    ];

    protected $casts = [
        'date'   => 'date',
        'status' => 'boolean',
        'deleted_at' => 'datetime',
    ];

    protected $attributes = [
        'status' => true,
    ];

    /** Relationships */
    public function homeDetails()
    {
        return $this->hasMany(HomeDetail::class, 'home_id');
    }

    public function images()
    {
        return $this->hasMany(Image::class, 'home_id');
    }

    public function details()
{
    return $this->hasMany(\App\Models\HomeDetail::class);
}

}
