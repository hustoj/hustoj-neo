<?php

namespace App;

use Illuminate\Support\Arr;

class Language
{
    const LANG_C = 0;
    const LANG_CPP = 1;
    const LANG_JAVA = 2;
    const LANG_PAS = 3;

    private static $languages = [
        self::LANG_C    => 'C',
        self::LANG_CPP  => 'C++',
        self::LANG_JAVA => 'Java',
        self::LANG_PAS  => 'Pascal',
    ];

    public static function allLanguages()
    {
        return self::$languages;
    }

    public static function showLang($id)
    {
        return Arr::get(self::$languages, $id);
    }
}
