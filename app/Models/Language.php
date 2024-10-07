<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    // public static $languages = [
    //     1 => 'en',
    //     2 => 'fr',
    //     3 => 'es',
    //     4 => 'it',
    //     5 => 'de',
    //     6 => 'gr',
    //     8 => 'pt',
    //     9 => 'ja',
    //     13 => 'cs',
    //     14 => 'da',
    //     15 => 'nl',
    //     16 => 'hu',
    //     17 => 'ms',
    //     18 => 'arb',
    //     19 => 'no',
    //     20 => 'pl',
    //     21 => 'sk',
    //     22 => 'sv',
    //     23 => 'hant',
    //     24 => 'fi',
    //     25 => 'ro',
    //     26 =>'hans',
    // ];

    public static function GetAllLanuages()
    {
        $language = Language::query()->where('show', '=', 1)->orderBy('ord','asc')->get()->toArray();
        return $language;
    }

    protected $table = 'language';
}
