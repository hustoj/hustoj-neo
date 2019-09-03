<?php

namespace App;

class Language
{
    const LANG_C = 0;
    const LANG_CPP = 1;
    const LANG_JAVA = 2;
    const LANG_PAS = 3;

    private static $languages = [
        0 => 'C',
        1 => 'C++',
        2 => 'Java',
        3 => 'Pascal',
    ];

    public static function allLanguages()
    {
        return self::$languages;
    }

    public static function showLang($id)
    {
        return array_get(self::$languages, $id);
    }
}
