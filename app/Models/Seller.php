<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Seller extends Model
{
    use HasFactory;

    public function payouts(): HasMany
    {
        return $this->hasMany(Payout::class);
    }
}
