<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CountryInfoCache extends Model
{
    use HasFactory;

    protected $table = 'country_info_cache';
    public $timestamps = false;
}
