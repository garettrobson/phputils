<?php
/**
* @author Garett Robson <info@garettrobson.co.uk
*/

declare(strict_types=1);

namespace PHPUtils;

use OutOfBoundsException;
use ArrayAccess;
use stdClass;

/**
*
* Class to offer convenience functions for common json_decode'ed objects.
*
*/
class JsonObject
{
    /**
    *
    * Combine objects into one.
    *
    * Takes a variable number *$sources* and combines them into the *$destination*.
    *
    * @param object $destination The objects to merge into a new object.
    * @param object $sources The objects to merge into a new object.
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
    * Merge objects into a new one.
    *
    * Takes a variable number *$sources* and produces a new, merged, one.
    *
    * @param object ...$sources The objects to merge into a new object.
    * @return object The new object.
    */
    public static function merge(object ...$sources)
    {
        return static::combine(new stdClass, ...$soruces);
    }

    /**
    *
    * Lookup a value in an object.
    *
    * Uses an *$address* to retrieve the values from a nested *$source* object
    *
    * **Example:**
    * ```
    * $o = json_decode("{ an: { example: { address: "value" } } }");
    *
    * JsonObject::get($o, "an.example.address"); // would return "value"
    * JsonObject::get($o, "a.bad.address"); // would return null
    * JsonObject::get($o, "a.bad.address", "default"); // would return "default"
    * JsonObject::get($o, "an/example/address", "default", "/"); // would return "value"
    * JsonObject::get($o, "a/bad/address", null, "/"); // would return null
    * ```
    *
    * @param object $source The object to search.
    * @param string $address The address of the value to return.
    * @param mixed $default The value to return when the value is not found.
    * @param string $delimiter The delimiter for the address.
    * @return mixed The value retrieved from the $source object.
    */
    public static function get(object $source, string $address, $default = null, string $delimiter = '.')
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

    /**
    *
    * Assign a value in an object.
    *
    * Uses an *$address* to retrieve the values from a nested *$source* object
    *
    * @param object $source The object to search.
    * @param string $address The address of the value to return.
    * @param mixed $value The value to set in the $source object.
    * @param string $delimiter The delimiter for the address.
    */
    public static function set(object $source, string $address, $value, string $delimiter = '.')
    {
        $parts = explode($delimiter, $address);
        $container = $source;
        while ($key = array_shift($parts)) {
            $type = gettype($container);
            switch ($type) {
                case 'array':
                    if (!array_key_exists($key, $container)) {
                        $contianer[$key] = new stdClass;
                    }
                    $container = &$contianer[$key];
                    break;
                case 'object':
                    if (!property_exists($container, $key)) {
                        $container->$key = new stdClass;
                    }
                    $container = &$container->$key;
                    break;
                default:
                    throw new OutOfBoundsException("Unable to assign value.");
            }
        }
        $container = $value;
    }
}
