<?php

namespace Smoren\StringFormatter;

use Smoren\Schemator\Components\NestedAccessor;
use Smoren\Schemator\Exceptions\PathNotExistException;

/**
 * StringFormatter class
 */
class StringFormatter
{
    public const DEFAULT_REGEXP = '/\{([A-Za-z0-9\-_\.]+)\}/';
    public const DEFAULT_PATH_DELIMITER = '.';

    /**
     * Formats string with given params
     * @param string $input input string
     * @param array<string, mixed> $params params map
     * @param bool $silent if true don't throw exception with catching errors
     * @param non-empty-string $regexp regular expression for masked params
     * @param non-empty-string $pathDelimiter path delimiter for nested param paths
     * @return string result string with replaced params masks
     * @throws StringFormatterException if not silent and some errors catched
     */
    public static function format(
        string $input,
        array $params,
        bool $silent = false,
        string $regexp = self::DEFAULT_REGEXP,
        string $pathDelimiter = self::DEFAULT_PATH_DELIMITER
    ): string {
        $accessor = new NestedAccessor($params, $pathDelimiter);
        $keyMap = static::findKeys($input, $regexp);

        $notFoundKeys = [];
        foreach($keyMap as $key => &$path) {
            try {
                $path = $accessor->get($path);
            } catch(PathNotExistException $e) {
                $notFoundKeys[] = $path;
                unset($keyMap[$key]);
            }
        }
        unset($path);

        if(!$silent && count($notFoundKeys)) {
            throw new StringFormatterException(
                'some keys not found in params array',
                StringFormatterException::ERROR_KEYS_NOT_FOUND,
                null,
                array_values($notFoundKeys)
            );
        }

        return str_replace(array_keys($keyMap), array_values($keyMap), $input);
    }

    /**
     * Formats string with given params in silent mode
     * @param string $input input string
     * @param array<string, mixed> $params params map
     * @param non-empty-string $regexp regular expression for masked params
     * @param non-empty-string $pathDelimiter path delimiter for nested param paths
     * @return string result string with replaced params masks
     */
    public static function formatSilent(
        string $input,
        array $params,
        string $regexp = self::DEFAULT_REGEXP,
        string $pathDelimiter = self::DEFAULT_PATH_DELIMITER
    ): string {
        return static::format($input, $params, true, $regexp, $pathDelimiter);
    }

    /**
     * Returns map of found keys to replace with params map
     * @param string $input input string
     * @param string $regexp regular expression for masked params
     * @return string[]
     */
    protected static function findKeys(string $input, string $regexp): array
    {
        preg_match_all($regexp, $input, $matches);
        $result = array_combine($matches[0] ?? [], $matches[1] ?? []);
        if(!$result) {
            return [];
        }
        return $result;
    }
}
