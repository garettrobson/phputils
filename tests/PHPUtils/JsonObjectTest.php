<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use PHPUtils\JsonObject;

final class EmailTest extends TestCase
{
    public function testSimpleCombineObjects(): void
    {

        $destination = json_decode('{"foo": "bar"}');
        $source = json_decode('{"baz": "qux"}');

        JsonObject::combine($destination, $source);

        $this->assertObjectHasAttribute('foo', $destination);
        $this->assertSame($destination->foo, "bar");

        $this->assertObjectHasAttribute('baz', $destination);
        $this->assertSame($destination->baz, "qux");
    }

    public function testSimpleOverwrite(): void
    {

        $destination = json_decode('{"overwrite":"original"}');
        $source = json_decode('{"overwrite": "overwriten"}');

        JsonObject::combine($destination, $source);

        $this->assertObjectHasAttribute('overwrite', $destination);
        $this->assertSame($destination->overwrite, "overwriten");
    }

    public function testSimpleArrayMerge(): void
    {

        $destination = json_decode('{"merge":["foo"]}');
        $source = json_decode('{"merge": ["bar"]}');

        JsonObject::combine($destination, $source);

        $this->assertObjectHasAttribute('merge', $destination);
        $this->assertSame($destination->merge, ['foo', 'bar']);
    }

    public function testDeepCombineObjects(): void
    {

        $destination = json_decode('{"key": {"foo": "bar"}}');
        $source = json_decode('{"key": {"baz": "qux"}}');

        JsonObject::combine($destination, $source);

        $this->assertObjectHasAttribute('key', $destination);

        $this->assertObjectHasAttribute('foo', $destination->key);
        $this->assertSame($destination->key->foo, "bar");

        $this->assertObjectHasAttribute('baz', $destination->key);
        $this->assertSame($destination->key->baz, "qux");

    }

    public function testCreateCombineObjects(): void
    {

        $destination = json_decode('{}');
        $source = json_decode('{"key": {"baz": "qux"}}');

        JsonObject::combine($destination, $source);

        $this->assertObjectHasAttribute('key', $destination);

        $this->assertObjectHasAttribute('baz', $destination->key);
        $this->assertSame($destination->key->baz, "qux");

    }

    public function testDeepOverwrite(): void
    {

        $destination = json_decode('{"key": {"overwrite":"original"}}');
        $source = json_decode('{"key": {"overwrite": "overwriten"}}');

        JsonObject::combine($destination, $source);

        $this->assertObjectHasAttribute('key', $destination);

        $this->assertObjectHasAttribute('overwrite', $destination->key);
        $this->assertSame($destination->key->overwrite, "overwriten");
    }

    public function testDeepArrayMerge(): void
    {

        $destination = json_decode('{"key": {"merge":["foo"]}}');
        $source = json_decode('{"key": {"merge": ["bar"]}}');

        JsonObject::combine($destination, $source);

        $this->assertObjectHasAttribute('key', $destination);

        $this->assertObjectHasAttribute('merge', $destination->key);
        $this->assertSame($destination->key->merge, ['foo', 'bar']);
    }
}
