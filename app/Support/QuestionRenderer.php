<?php

namespace App\Support;

class QuestionRenderer
{
    private static array $allowedTags = [
        'table', 'thead', 'tbody', 'tr', 'th', 'td',
        'b', 'strong', 'i', 'em', 'u', 'sub', 'sup',
        'br', 'hr', 'p', 'span', 'div',
        'ul', 'ol', 'li',
    ];

    private static array $allowedAttributes = [
        'class', 'style', 'colspan', 'rowspan', 'align', 'valign',
    ];

    public static function render(string $text): string
    {
        $text = self::sanitizeHtml($text);
        $text = nl2br($text);

        return $text;
    }

    private static function sanitizeHtml(string $html): string
    {
        $allowedTagsStr = '<' . implode('><', self::$allowedTags) . '>';
        $html = strip_tags($html, $allowedTagsStr);

        $html = preg_replace_callback(
            '/<(\w+)([^>]*)>/i',
            function ($matches) {
                $tag = strtolower($matches[1]);
                $attrs = $matches[2];

                if (! in_array($tag, self::$allowedTags, true)) {
                    return '';
                }

                $cleanAttrs = self::sanitizeAttributes($attrs);

                return '<' . $tag . $cleanAttrs . '>';
            },
            $html
        );

        return $html;
    }

    private static function sanitizeAttributes(string $attrString): string
    {
        if (trim($attrString) === '') {
            return '';
        }

        $allowed = [];
        preg_match_all('/(\w+)\s*=\s*["\']([^"\']*)["\']/', $attrString, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $name = strtolower($match[1]);
            $value = $match[2];

            if (in_array($name, self::$allowedAttributes, true)) {
                if ($name === 'style') {
                    $value = self::sanitizeStyle($value);
                }
                $allowed[] = $name . '="' . htmlspecialchars($value, ENT_QUOTES, 'UTF-8') . '"';
            }
        }

        return $allowed ? ' ' . implode(' ', $allowed) : '';
    }

    private static function sanitizeStyle(string $style): string
    {
        $safeProperties = [
            'text-align', 'vertical-align', 'width', 'height',
            'padding', 'margin', 'border', 'background-color', 'color',
            'font-weight', 'font-size', 'font-style',
        ];

        $parts = explode(';', $style);
        $clean = [];

        foreach ($parts as $part) {
            $part = trim($part);
            if (! $part || ! str_contains($part, ':')) {
                continue;
            }

            [$prop, $val] = explode(':', $part, 2);
            $prop = strtolower(trim($prop));
            $val = trim($val);

            if (in_array($prop, $safeProperties, true) && ! preg_match('/expression|javascript|url/i', $val)) {
                $clean[] = $prop . ':' . $val;
            }
        }

        return implode('; ', $clean);
    }
}
