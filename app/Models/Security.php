<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Security extends Model
{
    protected $fillable = ['security_type_id', 'symbol'];

    // Each security belongs to one type
    public function type()
    {
        return $this->belongsTo(SecurityType::class, 'security_type_id');
    }

    // Retrieve latest price
    public function price()
    {
        return $this->hasOne(SecurityPrice::class)->latestOfMany();
    }
}
