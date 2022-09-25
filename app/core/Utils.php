<?php

declare(strict_types=1);

namespace Core;

class Utils
{
    /**
     * @param string $string
     * @return string
     */
    public static function camelCaseToUnderscore(string $string): string
    {
        $str = lcfirst($string);
        return preg_replace_callback(
            '/([A-Z])/',
            static function ($matches) {
                return '_' . strtolower($matches[0]);
            },
            $str
        );
    }

    /**
     * @param string $string
     * @return string
     */
    public static function underscoreToCamelCase(string $string): string
    {
        return str_replace(' ', '', ucwords(str_replace('_', ' ', $string)));
    }

    /**
     * @param int $length
     * @return string
     * @throws \Exception
     */
    public static function randomString(int $length): string
    {
        $alphabet = [...range(0, 9), ...range('a', 'z'), ...range('A', 'Z')];

        $charactersLength = count($alphabet);

        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $randomString .= $alphabet[random_int(0, $charactersLength - 1)];
        }

        return $randomString;
    }
}
