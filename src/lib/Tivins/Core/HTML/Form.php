<?php

namespace Tivins\Core\HTML;

use Tivins\Core\Code\Exception;
use Tivins\Core\StringUtil as Str;

class Form
{
    /**
     * @throws Exception
     */
    public static function form(string $formId, string $formAction, string $formContent, string $method = "post"): string
    {
        return '<form method="' . $method . '" action="' . $formAction . '">'
            . self::hidden('hash', FormSecurity::getPublicToken($formId))
            . $formContent
            . '</form>';
    }

    public static function hidden(string $name, string $value): string
    {
        return '<input type="hidden" name="' . Str::html($name) . '" value="' . Str::html($value) . '">';
    }

    public static function select(string $name, array $options, array $selected = [], array $opts = []): string
    {
        $opts  += [
            'class' => '',
        ];
        $attrs = '';
        if ($opts['class']) {
            $attrs .= ' class="' . $opts['class'] . '"';
        }
        $html = '<select name="' . $name . '"' . $attrs . '>';
        foreach ($options as $key => $value) {
            $attrs = in_array($key, $selected) ? ' selected' : '';
            $html  .= '<option value="' . Str::html($key) . '"' . $attrs . '>' . Str::html($value) . '</option>';
        }
        $html .= '</select>';
        return $html;
    }
}