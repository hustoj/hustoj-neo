<?php

namespace App;

use Illuminate\Support\Arr;

class Language
{
    public const LANG_C = 0;
    public const LANG_CPP = 1;
    public const LANG_PAS = 2;
    public const LANG_JAVA = 3;

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
