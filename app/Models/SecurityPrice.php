<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SecurityPrice extends Model
{
    protected $fillable = ['security_id', 'last_price', 'as_of_date'];
}
