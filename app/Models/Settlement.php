<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Settlement extends Model
{
    use HasFactory;

    protected $fillable = ['county_id', 'name'];

    public function county()
    {
        return $this->belongsTo(County::class);
    }

    public function zipCodes()
    {
        return $this->hasMany(ZipCode::class);
    }
}
