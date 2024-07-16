<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    use HasFactory;

    public static $languages = [
        'en' => 1,
        'fr' => 2,
        'es' => 3,
        'it' => 4,
        'de' => 5,
        'gr' => 6,
        'pt' => 8,
        'ja' => 9,
        'cs' => 13,
        'da' => 14,
        'nl' => 15,
        'hu' => 16,
        'ms' => 17,
        'arb' => 18,
        'no' => 19,
        'pl' => 20,
        'sk' => 21,
        'sv' => 22,
        'hant' => 23,
        'fi' => 24,
        'ro' => 25,
        'hans' => 26,
    ];

    protected $table = 'language';
}