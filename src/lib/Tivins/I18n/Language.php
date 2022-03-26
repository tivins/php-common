<?php

namespace Tivins\I18n;

enum Language: string
{
    case English = 'en';
    case French = 'fr';
    case Italian = 'it';
    case German = 'de';
    case Spain = 'es';
    // ...

    private const DATA = [
        'af'      => 'Afrikaans',
        'id'      => 'Bahasa Indonesia',
        'ms'      => 'Bahasa Melayu',
        'ca'      => 'Català',
        'cs'      => 'Čeština',
        'da'      => 'Dansk',
        'de'      => 'Deutsch',
        'et'      => 'Eesti',
        'en'      => 'English',
        'en_gb'   => 'English (United Kingdom)',
        'es'      => 'Español',
        'es_419'  => 'Español (Latinoamérica)',
        'eu'      => 'Euskara',
        'fil'     => 'Filipino',
        'fr'      => 'Français',
        'fr_ca'   => 'Français (Canada)',
        'gl'      => 'Galego',
        'hr'      => 'Hrvatski',
        'zu'      => 'Isizulu',
        'is'      => 'Íslenska',
        'it'      => 'Italiano',
        'sw'      => 'Kiswahili',
        'lv'      => 'Latviešu',
        'lt'      => 'Lietuvių',
        'hu'      => 'Magyar',
        'nl'      => 'Nederlands',
        'no'      => 'Norsk',
        'pl'      => 'Polski',
        'pt_br'   => 'Português (Brasil)',
        'pt_pt'   => 'Português (Portugal)',
        'ro'      => 'Română',
        'sk'      => 'Slovenčina',
        'sl'      => 'Slovenščina',
        'sr_latn' => 'Srpski',
        'fi'      => 'Suomi',
        'sv'      => 'Svenska',
        'vi'      => 'Tiếng Việt',
        'tr'      => 'Türkçe',
        'el'      => 'Ελληνικά',
        'bg'      => 'Български',
        'ru'      => 'Русский',
        'sr'      => 'Српски',
        'uk'      => 'Українська',
        'iw'      => '‫עברית‬',
        'ur'      => '‫اردو‬',
        'ar'      => '‫العربية‬',
        'fa'      => '‫فارسی‬',
        'am'      => 'አማርኛ',
        'mr'      => 'मराठी',
        'hi'      => 'हिन्दी',
        'bn'      => 'বাংলা',
        'gu'      => 'ગુજરાતી',
        'ta'      => 'தமிழ்',
        'te'      => 'తెలుగు',
        'kn'      => 'ಕನ್ನಡ',
        'ml'      => 'മലയാളം',
        'th'      => 'ไทย',
        'ko'      => '한국어',
        'zh_hk'   => '中文 (香港)',
        'zh_cn'   => '中文（简体中文）',
        'zh_tw'   => '中文（繁體中文）',
        'ja'      => '日本語',
    ];

    public function getNatural(): string
    {
        return self::DATA[$this->value] ?? $this->name;
    }
}