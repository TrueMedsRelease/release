<?php

namespace App\Services;

use App\Models\Language;

class LanguageServices{
    public static function getAllLanguages() {
        $languages = Language::query()
            ->where('show', '=', '1')
            ->orderBy('ord', 'asc')
            ->get()
            ->toArray();

        return $languages;
    }
}