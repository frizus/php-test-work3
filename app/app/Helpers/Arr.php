<?php

namespace App\Helpers;

class Arr
{
    public static function set(mixed &$array, string|array $key, mixed $value): void
    {
        if (!is_array($key)) {
            $key = explode('.', $key);
        }

        if (empty($key)) {
            $array = $value;
        }

        if (!is_array($array) && !is_object($array)) {
            $array = [];
        }

        $lastKey = array_pop($key);
        $element = &$array;
        foreach ($key as $singleKey) {
            if (is_array($element) && key_exists($singleKey, $element)) {
                $element = &$element[$singleKey];
            } elseif (($element instanceof \ArrayAccess) && $element->offsetExists($singleKey)) {
                $element = &$element[$singleKey];
            } elseif (is_object($element) && property_exists($element, $singleKey)) {
                $element = &$element->$singleKey;
            } else {
                if (!is_array($element) && !is_object($element)) {
                    $element = [];
                }

                if (is_array($element)) {
                    $element[$singleKey] = [];
                    $element = &$element[$singleKey];
                } elseif (is_object(($element))) {
                    $element->$singleKey = [];
                    $element = &$element->$singleKey;
                }
            }
        }

        $element[$lastKey] = $value;
    }

    public static function pluckUnique(mixed $array, string|array $key): array
    {
        $column = static::pluck($array, $key);
        return array_unique($column);
    }

    public static function pluck(mixed $array, string|array $key, bool $preserveKeys = false): array
    {
        $column = [];
        $i = 0;
        foreach ($array as $cursor => $value) {
            $columnKey = $preserveKeys ? $cursor : $i++;
            $column[$columnKey] = static::get($key, $value);
        }

        return $column;
    }

    public static function filterOutEmpty(mixed $array): array
    {
        return static::filter($array, fn ($value) => !empty($value));
    }

    public static function filter(mixed $array, \Closure|null $filter = null): array
    {
        foreach ($array as $key => $value) {
            if (!$filter) {
                if ($value === null) {
                    unset($array[$key]);
                }
            } else {
                if (!$filter($value, $key)) {
                    unset($array[$key]);
                }
            }
        }

        return $array;
    }

    public static function wrap(mixed $array): array
    {
        if (is_array($array)) {
            return $array;
        }

        if (is_null($array)) {
            return [];
        }

        return [$array];
    }

    public static function getSafely(string|array $key, mixed $array, mixed $default = null): mixed
    {
        try {
            return static::get($key, $array, $default);
        } catch (\Throwable $e) {
            return $default;
        }
    }

    public static function get(string|array $key, mixed $array, mixed $default = null): mixed
    {
        if (!is_array($array) &&
            !is_object($array)
        ) {
            return $default;
        }

        if (!is_array($key)) {
            $key = explode('.', $key);
        }

        if (empty($key)) {
            return $array;
        }

        $element = $array;
        foreach ($key as $singleKey) {
            if ((is_array($element)) && array_key_exists($singleKey, $element)) {
                $element = $element[$singleKey];
            } elseif (($element instanceof \ArrayAccess) && $element->offsetExists($singleKey)) {
                $element = $element[$singleKey];
            } elseif (is_object($element) && property_exists($element, $singleKey)) {
                $element = $element->$singleKey;
            } else {
                return $default;
            }
        }

        return $element;
    }

    public static function has(string|array $key, $array): bool
    {
        if (!is_array($array) &&
            !is_object($array)
        ) {
            return false;
        }

        if (!is_array($key)) {
            $key = explode('.', $key);
        }

        if (empty($key)) {
            return true;
        }

        $element = $array;
        foreach ($key as $singleKey) {
            if (is_array($element) && array_key_exists($singleKey, $element)) {
                $element = $element[$singleKey];
            } elseif (($element instanceof \ArrayAccess) && $element->offsetExists($singleKey)) {
                $element = $element[$singleKey];
            } elseif (is_object($element) && property_exists($element, $singleKey)) {
                $element = $element->$singleKey;
            } else {
                return false;
            }
        }

        return true;
    }

    public static function forgetMultiple(mixed &$array, array $keys): void
    {
        foreach ($keys as $key) {
            static::forget($array, $key);
        }
    }

    public static function forget(mixed &$array, string|array $key): void
    {
        if (!is_array($key)) {
            $key = explode('.', $key);
        }

        if (empty($key)) {
            return;
        }

        $lastKey = array_pop($key);
        $element = &$array;
        foreach ($key as $singleKey) {
            if (is_array($element) && key_exists($singleKey, $element)) {
                $element = &$element[$singleKey];
            } elseif (is_object($element) && property_exists($element, $singleKey)) {
                $element = &$element->$singleKey;
            } else {
                return;
            }
        }

        if (is_array($element) && key_exists($lastKey, $element)) {
            unset($element[$lastKey]);
        } elseif (is_object($element) && property_exists($element, $lastKey)) {
            unset($element->$lastKey);
        }
    }

    public static function isIndexed(mixed $array): bool
    {
        return !self::isAssoc($array);
    }

    public static function isAssoc(mixed $array): bool
    {
        $keys = array_keys($array);
        return $keys !== array_keys($keys);
    }

    public static function merge(mixed $array, mixed ...$arrays): array
    {
        if (!is_array($array)) {
            $array = [];
        }

        foreach ($arrays as $singleArray) {
            foreach ($singleArray as $key => $value) {
                if (is_array($array[$key]) && is_array($value)) {
                    $array[$key] = static::_merge($array[$key], $value);
                } else {
                    $array[$key] = $value;
                }
            }
        }

        return $array;
    }

    protected static function _merge(mixed $array, mixed $array2): mixed
    {
        foreach ($array2 as $key => $value) {
            if (is_array($array[$key]) && is_array($value)) {
                $array[$key] = static::_merge($array[$key], $value);
            } else {
                $array[$key] = $value;
            }
        }

        return $array;
    }
}
