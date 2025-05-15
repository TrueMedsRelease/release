<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

class Language extends Model
{
    use HasFactory;

    public static array $languages = [
        'en'   => 1,
        'fr'   => 2,
        'es'   => 3,
        'it'   => 4,
        'de'   => 5,
        'gr'   => 6,
        'pt'   => 8,
        'ja'   => 9,
        'cs'   => 13,
        'da'   => 14,
        'nl'   => 15,
        'hu'   => 16,
        'ms'   => 17,
        'arb'  => 18,
        'no'   => 19,
        'pl'   => 20,
        'sk'   => 21,
        'sv'   => 22,
        'hant' => 23,
        'fi'   => 24,
        'ro'   => 25,
        'hans' => 26,
    ];

    public static $languages_name = [
        'en'   => 'English',
        'fr'   => 'Française',
        'es'   => 'Español',
        'it'   => 'Italiano',
        'de'   => 'Deutsch',
        'gr'   => 'Ελληνική',
        'pt'   => 'Português',
        'ja'   => '日本語',
        'cs'   => 'Čeština',
        'da'   => 'Dansk',
        'nl'   => 'Nederlands',
        'hu'   => 'Magyar nyelv',
        'ms'   => 'Bahasa Melayu',
        'arb'  => 'اللغة العربية الفصحى الحديثة',
        'no'   => 'Norsk',
        'pl'   => 'Polszczyzna',
        'sk'   => 'Slovenský',
        'sv'   => 'Svenska',
        'hant' => '繁體字',
        'fi'   => 'Suomi',
        'ro'   => 'Limba română',
        'hans' => '简体字',
    ];

    public static function GetAllLanuages()
    {
        $language = Language::query()
            ->where('show', '=', 1)
            ->orderBy('ord', 'asc')
            ->get()
            ->toArray();

        return $language;
    }

    public static function GetLanguageByCountry($country)
    {
        $preferredLanguage = request()->getPreferredLanguage();

        $language = Language::query()
            ->where('show', '=', 1)
            ->where('code', '=', $preferredLanguage)
            ->first('code');
        if (empty($language)) {
            $language = Language::query()
                ->where('show', '=', 1)
                ->where('country_iso2', 'LIKE', '%' . $country . '%')
                ->first('code');
        }
        if (empty($language)) {
            $languages = Language::GetAllLanuages();

            if (count($languages) > 1) {
                return App::currentLocale();
            } else {
                if (count($languages) == 1) {
                    $languageCode = $languages[0]['code'];

                    if ($languageCode == App::currentLocale()) {
                        return App::currentLocale();
                    } else {
                        return config('app.language');
                    }
                } else {
                    return config('app.language');
                }
            }
        } else {
            $language = $language->toArray();
            return $language['code'];
        }
    }

    protected $table = 'language';
}
