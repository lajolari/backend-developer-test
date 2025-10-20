<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SecurityType extends Model
{
    protected $fillable = ['slug', 'name'];

    // A type can have many securities
    public function securities()
    {
        return $this->hasMany(Security::class);
    }
}
