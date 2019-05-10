<?php
declare(strict_types=1);

namespace PHPUtils;

use ArrayAccess;
use stdClass;

class JsonObject
{
    public static function combine(stdClass $destination, stdClass ...$sources)
    {
        foreach($sources as $source)
        {
            foreach($source as $property => $value)
            {
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

    public static function merge(stdClass ...$sources)
    {
        return static::combine(new stdClass, ...$soruces);
    }
}
