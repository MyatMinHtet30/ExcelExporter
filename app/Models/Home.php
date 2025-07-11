<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Home extends Model
{
    use HasFactory;

    protected $fillable = [
        "project_name",
        "dear",
        "trooper",       // Assuming you want to keep this even if not in migration
        "date",          // ✅ Fixed typo from "data"
        "house_no",
        "list_name",
        "status",
    ];

    /**
     * Relationships (optional)
     * Add these if you're using related models:
     */

    public function homeDetails()
    {
        return $this->hasMany(HomeDetail::class, 'home_id');
    }

    public function images()
    {
        return $this->hasMany(Image::class, 'home_id');
    }
}
