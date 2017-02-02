<?php

namespace CloudCreativity\JsonApi\Utils;

class Str
{

    /**
     * @var array
     */
    private static $dasherized = [];

    /**
     * @var array
     */
    private static $decamelized = [];

    /**
     * @var array
     */
    private static $camelized = [];

    /**
     * @var array
     */
    private static $classified = [];

    /**
     * Replaces underscores or camel case with dashes.
     *
     * @param string $value
     * @return string
     */
    public static function dasherize($value)
    {
        if (isset(self::$dasherized[$value])) {
            return self::$dasherized[$value];
        }

        return self::$dasherized[$value] = str_replace('_', '-', self::decamelize($value));
    }

    /**
     * Converts a camel case string into all lower case separated by underscores.
     *
     * @param string $value
     * @return string
     */
    public static function decamelize($value)
    {
        if (isset(self::$decamelized[$value])) {
            return self::$decamelized[$value];
        }

        return self::$decamelized[$value] = strtolower(preg_replace('/(.)(?=[A-Z])/u', '$1_', $value));
    }

    /**
     * Gets the lower camel case form of a string.
     *
     * @param string $value
     * @return string
     */
    public static function camelize($value)
    {
        if (isset(self::$camelized[$value])) {
            return self::$camelized[$value];
        }

        return self::$camelized[$value] = lcfirst(self::classify($value));
    }

    /**
     * Gets the upper camel case form of a string.
     *
     * @param string $value
     * @return string
     */
    public static function classify($value)
    {
        if (isset(self::$classified[$value])) {
            return self::$classified[$value];
        }

        $converted = ucwords(str_replace(['-', '_'], ' ', $value));

        return self::$classified[$value] = str_replace(' ', '', $converted);
    }
}
