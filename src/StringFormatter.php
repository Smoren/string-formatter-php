<?php

namespace Smoren\StringFormatter;

/**
 * StringFormatter class
 */
class StringFormatter
{
    public const DEFAULT_REGEXP = '/\{([A-Za-z0-9\-_]+)\}/';

    /**
     * @param string $input
     * @param array<string, mixed> $params
     * @param bool $silent
     * @param string $regexp
     * @return string
     * @throws StringFormatterException
     */
    public static function format(
        string $input,
        array $params,
        bool $silent = false,
        string $regexp = self::DEFAULT_REGEXP
    ): string {
        $keyMap = static::findKeys($input, $regexp);

        if(!$silent && count($notFoundKeys = array_diff(array_values($keyMap), array_keys($params)))) {
            throw new StringFormatterException(
                'some keys not found in params array',
                StringFormatterException::ERROR_KEYS_NOT_FOUND,
                null,
                array_values($notFoundKeys)
            );
        }

        foreach($keyMap as $key => &$val) {
            if(!isset($params[$val])) {
                unset($keyMap[$key]);
            } else {
                $val = $params[$val];
            }
        }
        unset($val);

        return str_replace(array_keys($keyMap), array_values($keyMap), $input);
    }

    /**
     * @param string $input
     * @param array<string, mixed> $params
     * @param string $regexp
     * @return string
     */
    public static function formatSilent(
        string $input,
        array $params,
        string $regexp = self::DEFAULT_REGEXP
    ): string {
        return static::format($input, $params, true, $regexp);
    }

    /**
     * @param string $input
     * @param string $regexp
     * @return string[]
     */
    protected static function findKeys(string $input, string $regexp): array
    {
        preg_match_all($regexp, $input, $matches);
        $result = array_combine($matches[0] ?? [], $matches[1] ?? []);
        if($result === false) {
            return [];
        }
        return $result;
    }
}
