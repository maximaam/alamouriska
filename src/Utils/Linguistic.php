<?php
/**
 * Created by PhpStorm.
 * User: mimosa
 * Date: 07.01.19
 * Time: 11:56
 */

declare(strict_types=1);

namespace App\Utils;

/**
 * Class Linguistic
 * @package App\Utils
 */
class Linguistic
{
    /**
     * Known accents
     */
    const ACCENTS = [
        'Š' => 'S', 'š' => 's', 'Ð' => 'Dj', 'Ž' => 'Z', 'ž' => 'z', 'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A',
        'Å' => 'A', 'Æ' => 'A', 'Ç' => 'C', 'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I',
        'Ï' => 'I', 'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Ø' => 'O', 'Ù' => 'U', 'Ú' => 'U',
        'Û' => 'U', 'Ü' => 'U', 'Ý' => 'Y', 'Þ' => 'B', 'ß' => 'Ss', 'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a',
        'å' => 'a', 'æ' => 'a', 'ç' => 'c', 'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i',
        'ï' => 'i', 'ð' => 'o', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ø' => 'o', 'ù' => 'u',
        'ú' => 'u', 'û' => 'u', 'ý' => 'y', 'þ' => 'b', 'ÿ' => 'y', 'ƒ' => 'f', 'c' => 'c', '‡' => 't',
    ];

    /**
     * @param string $char
     * @return string
     */
    public static function replaceAccents(string $char): string
    {
        return \strtr($char, self::ACCENTS);
    }


    /**
     * @param array $needle
     * @param $haystack
     * @param bool $caseInsensitive
     * @return array
     */
    public static function contains(array $needle, $haystack, $caseInsensitive = true): array
    {
        $data = [];

        foreach (\array_chunk($needle, 2) as $chunk) {
            $exp = '/\b' . \implode('\b|\b', \array_map('preg_quote', $chunk)) . '\b' . ($caseInsensitive ? '/ui' : '/u');

            @preg_match_all($exp, $haystack, $matches);

            $data = \array_merge($data, $matches[0]);
        }

        return $data;
    }

    /**
     * Create a web friendly URL slug from a string.
     *
     * Although supported, transliteration is discouraged because
     *     1) most web browsers support UTF-8 characters in URLs
     *     2) transliteration causes a loss of information
     *
     * @author Sean Murphy <sean@iamseanmurphy.com>
     * @copyright Copyright 2012 Sean Murphy. All rights reserved.
     * @license http://creativecommons.org/publicdomain/zero/1.0/
     * @see https://gist.github.com/sgmurphy/3098978
     *
     * @param string $str
     * @param array $options
     * @return string
     */
    public static function toSlug(string $str, array $options = []) : string
    {
        // Make sure string is in UTF-8 and strip invalid UTF-8 characters
        $str = \mb_convert_encoding((string)$str, 'UTF-8', \mb_list_encodings());

        $defaults = [
            'delimiter' => '-',
            'limit' => null,
            'lowercase' => true,
            'replacements' => [
                '/(\w)(\')(\w)/' => '$1$3',
                '/(\w)(`)(\w)/' => '$1$3',
                '/(\s)(&)(\s)/' => '-',
                '/(\w)(&)(\w)/' => '$1-$3'
            ],
            'transliterate' => true,
        ];

        // Merge options
        $options = \array_merge($defaults, $options);

        // Make custom replacements
        $str = \preg_replace(\array_keys($options['replacements']), $options['replacements'], $str);

        // Transliterate characters to ASCII
        if ($options['transliterate']) {
            $str = \str_replace(\array_keys(self::ACCENTS), self::ACCENTS, $str);
        }

        // Replace non-alphanumeric characters with our delimiter
        $str = \preg_replace('/[^\p{L}\p{Nd}\-]+/u', $options['delimiter'], $str);

        // Remove duplicate delimiters
        $str = \preg_replace('/(' . \preg_quote($options['delimiter'], '/') . '){2,}/', '$1', $str);

        // Truncate slug to max. characters
        $str = \mb_substr($str, 0, ($options['limit'] ? $options['limit'] : \mb_strlen($str, 'UTF-8')), 'UTF-8');

        // Remove delimiter from ends
        $str = \trim($str, $options['delimiter']);

        return $options['lowercase'] ? \mb_strtolower($str, 'UTF-8') : $str;
    }
}