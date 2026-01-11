<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ZipCode extends Model
{
    use HasFactory;

    protected $fillable = ['settlement_id', 'zip_code'];

    // EZ A RÉSZ HIÁNYOZHAT:
    public function settlement()
    {
        return $this->belongsTo(Settlement::class);
    }
}
