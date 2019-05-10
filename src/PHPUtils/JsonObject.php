<?php
/**
* @author Garett Robson <info@garettrobson.co.uk
*/

declare(strict_types=1);

namespace PHPUtils;

use ArrayAccess;
use stdClass;
use object;
use string;

/**
*
* Class to offer convenience functions for common json_decode'ed objects.
*
*/
class JsonObject
{
    /**
    *
    * Combine a variable number soruce stdClass and merges them into the destination stdClass.
    *
    * @param object $destination The objects to merge into a new object.
    * @param object ...$sources The objects to merge into a new object.
    * @return object The new $destination object, with all merged properties.
    */
    public static function combine(object $destination, object ...$sources)
    {
        foreach ($sources as $source) {
            foreach ($source as $property => $value) {
                $valueType = gettype($value);
                if (
                    $valueType === 'array' &&
                    property_exists($destination, $property) &&
                    is_array($destination->$property)
                ) {
                    $destination->$property = array_merge($destination->$property, $value);
                } else {
                    if ($valueType === 'object') {
                        $destination->$property = static::combine($destination->$property ?? new stdClass, $value);
                    } else {
                        $destination->$property = $value;
                    }
                }
            }
        }
        return $destination;
    }

    /**
    *
    * Combine a variable number of stdClass objects into a new object.
    *
    * @param object ...$sources The objects to merge into a new object.
    * @return stdClass The new object.
    */
    public static function merge(object ...$sources)
    {
        return static::combine(new stdClass, ...$soruces);
    }

    /**
    *
    * Uses a string-based address to retrieve values from a nested object
    *
    * Example:
    *  $o = { an: { example: { address: "value" } } }
    *  get($o, "an.example.address") // would return "value"
    *  get($o, "a.bad.address") // would return null
    *  get($o, "a.bad.address", "default") // would return "default"
    *  get($o, "an/example/address", "default", "/") // would return "value"
    *  get($o, "a/bad/address", null, "/") // would return null
    *
    * @param object $source The object to search.
    * @return string $address The address of the value to return.
    * @return mixed $default The value to return when the value is not found.
    * @return string $delimiter The delimiter for the address.
    */
    public static function get(object $source, string $address, $default = null, $delimiter = '.')
    {
        $parts = explode($delimiter, $address);
        $container = $source;
        while ($key = array_shift($parts)) {
            $type = gettype($container);
            switch ($type) {
                case 'array':
                    if (!array_key_exists($key, $container)) {
                        return $default;
                    }
                    $container = $contianer[$key];
                    break;
                case 'object':
                    if (!property_exists($container, $key)) {
                        return $default;
                    }
                    $container = $container->$key;
                    break;
                default:
                    return $default;
            }
        }
        return $container;
    }
}
