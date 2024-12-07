<?php

class ArrayDiffMultidimensional
{

    public static function compareInsertsDeletes(array $array1, array $array2, $strict = true): array
    {
        if (!is_array($array1)) {
            throw new \InvalidArgumentException('$array1 must be an array!');
        }

        if (!is_array($array2)) {
            return $array1;
        }

        $result = [];

        $differences = [];
        $deletes = [];
        $inserts = [];

        foreach ($array1 as $key => $value) {

            if (!array_key_exists($key, $array2)) {
                $result[$key] = $value;
                continue;
            }

            if (is_array($value) && count($value) > 0) {
                $recursiveArrayDiff = static::compareInsertsDeletes($value, $array2[$key], $strict);
                if (count($recursiveArrayDiff) > 0) {
                    $result[$key] = $recursiveArrayDiff;
                }
                continue;
            }

            $value1 = $value;
            $value2 = $array2[$key];

            if ($strict ? is_float($value1) && is_float($value2) : is_float($value1) || is_float($value2)) {
                $value1 = (string) $value1;
                $value2 = (string) $value2;
            }

            if ($strict ? $value1 !== $value2 : $value1 != $value2) {
                $result[$key] = $value;
            }

            // generate text output
            if ((int)$value1 !== (int)$value2) {
                $differences[] = "  Difference at table '$key' : $result[$key] vs. $value2";
            }

            if ((int)$value1 > (int)$value2) {
                $deletes[] = "  Table '$key' [DELETE] - OLD: $result[$key] vs. NEW: $value2";
            }

            if ((int)$value1 < (int)$value2) {
                $inserts[] = "  Table '$key' [INSERT] - OLD: $result[$key] vs. NEW: $value2";
            }
        }
        return ['differences' => $differences, 'deletes' => $deletes, 'inserts' => $inserts];
    }

    /**
     * Returns an array with the differences between $array1 and $array2
     * $strict variable defines if comparison must be strict or not
     *
     * @param array $array1
     * @param array $array2
     * @param bool $strict
     *
     * @return array
     */
    public static function compare(array $array1, array $array2, bool $strict = true): array
    {
        if (!is_array($array1)) {
            throw new \InvalidArgumentException('$array1 must be an array!');
        }

        if (!is_array($array2)) {
            return $array1;
        }

        $result = array();

        foreach ($array1 as $key => $value) {
            if (!array_key_exists($key, $array2)) {
                $result[$key] = $value;
                continue;
            }

            if (is_array($value) && count($value) > 0) {
                $recursiveArrayDiff = static::compare($value, $array2[$key], $strict);

                if (count($recursiveArrayDiff) > 0) {
                    $result[$key] = $recursiveArrayDiff;
                }

                continue;
            }

            $value1 = $value;
            $value2 = $array2[$key];

            if ($strict ? is_float($value1) && is_float($value2) : is_float($value1) || is_float($value2)) {
                $value1 = (string) $value1;
                $value2 = (string) $value2;
            }

            if ($strict ? $value1 !== $value2 : $value1 != $value2) {
                $result[$key] = $value;
            }
        }

        return $result;
    }

    /**
     * Returns an array with a strict comparison between $array1 and $array2
     *
     * @param array $array1
     * @param array $array2
     *
     * @return array
     */
    public static function strictComparison(array $array1, array $array2): array
    {
        return static::compare($array1, $array2, true);
    }

    /**
     * Returns an array with a loose comparison between $array1 and $array2
     *
     * @param array $array1
     * @param array $array2
     *
     * @return array
     */
    public static function looseComparison(array $array1, array $array2): array
    {
        return static::compare($array1, $array2, false);
    }
}