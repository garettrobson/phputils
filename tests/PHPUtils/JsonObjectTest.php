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
        $this->assertObjectHasAttribute('baz', $destination);

        $this->assertSame($destination->foo, "bar");
        $this->assertSame($destination->baz, "qux");
    }

    public function testSimpleOverwriteCombine(): void
    {
        $destination = json_decode('{"overwrite":"original"}');
        $source = json_decode('{"overwrite": "overwriten"}');

        JsonObject::combine($destination, $source);

        $this->assertObjectHasAttribute('overwrite', $destination);

        $this->assertSame($destination->overwrite, "overwriten");
    }

    public function testSimpleArrayCombine(): void
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
        $this->assertObjectHasAttribute('baz', $destination->key);

        $this->assertSame($destination->key->foo, "bar");
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

    public function testDeepOverwriteCombine(): void
    {
        $destination = json_decode('{"key": {"overwrite":"original"}}');
        $source = json_decode('{"key": {"overwrite": "overwriten"}}');

        JsonObject::combine($destination, $source);

        $this->assertObjectHasAttribute('key', $destination);
        $this->assertObjectHasAttribute('overwrite', $destination->key);

        $this->assertSame($destination->key->overwrite, "overwriten");
    }

    public function testDeepArrayCombine(): void
    {
        $destination = json_decode('{"key": {"merge":["foo"]}}');
        $source = json_decode('{"key": {"merge": ["bar"]}}');

        JsonObject::combine($destination, $source);

        $this->assertObjectHasAttribute('key', $destination);
        $this->assertObjectHasAttribute('merge', $destination->key);

        $this->assertSame($destination->key->merge, ['foo', 'bar']);
    }

    public function testGet(): void
    {
        $search = json_decode('{"foo": {"bar": "baz", "qux": ["quux", "quuz", "corge"]}}');

        $value = JsonObject::get($search, "foo.bar");
        $this->assertSame($value, "baz");

        $value = JsonObject::get($search, "foo.qux");
        $this->assertSame($value, ["quux", "quuz", "corge"]);

        $value = JsonObject::get($search, "invalid.key", "default");
        $this->assertSame($value, "default");
    }

    public function testSet(): void
    {
        $search = json_decode('{"foo": {"bar": "baz", "qux": ["quux", "quuz", "corge"]}}');

        JsonObject::set($search, "some.key", "value");
        $this->assertSame($search->some->key, "value");

        JsonObject::set($search, "foo.bar", 123);
        $this->assertSame($search->foo->bar, 123);
    }

    public function testSetCreates(): void
    {
        $search = json_decode('{}');

        JsonObject::set($search, "some.key", "value");
        $this->assertSame($search->some->key, "value");
    }

    public function testSetFailure(): void
    {
        $search = json_decode('{"foo": {"bar": "baz", "qux": ["quux", "quuz", "corge"]}}');

        $this->expectException(TypeError::class);
        JsonObject::set($search, "foo.bar.value", "value");
    }

    public function testRemove(): void
    {
        $search = json_decode('{"foo": {"bar": "baz", "qux": ["quux", "quuz", "corge"]}}');

        $this->assertTrue(JsonObject::remove($search, "foo.bar"));
        $this->assertFalse(JsonObject::remove($search, "foo.barasds"));
        $this->assertFalse(JsonObject::remove($search, "adasdfoo.barasds"));
        $this->assertFalse(property_exists($search->foo, 'bar'));
    }

    public function testExists(): void
    {
        $search = json_decode('{"foo": {"bar": "baz", "qux": ["quux", "quuz", "corge"]}}');

        $this->assertTrue(JsonObject::exists($search, "foo.bar"));
        $this->assertFalse(JsonObject::exists($search, "foo.barasdasd"));
    }

    public function testLoadString(): void
    {
        $string = '{"foo": {"bar": "baz", "qux": ["quux", "quuz", "corge"]}}';
        $object = JsonObject::loadString($string);
        $this->assertIsObject($object);
    }

    public function testLoadStringFailure(): void
    {
        $string = '{{"foo": {"bar": "baz", "qux": ["quux", "quuz", "corge"]}}';
        $this->expectException(\ParseError::class);
        $object = JsonObject::loadString($string);
    }

    public function testLoadFile(): void
    {
        $this->expectException(\ParseError::class);
        $object = JsonObject::loadFile(__FILE__);
    }

    public function testLoadNotAFile(): void
    {
        $this->expectException(\RuntimeException::class);
        $object = JsonObject::loadFile(__DIR__);
    }
}
