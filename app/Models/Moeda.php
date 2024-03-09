<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Moeda extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'symbol', 'slug','ranking', 'market_cap', 'price', 'volume_24h', 'variacao_24h', 'dados'];
}
