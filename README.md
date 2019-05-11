# PHP Utils

A collection of PHP classes which offer general convenience for common tasks
which are not supported out-of-the box by the language.

## Classes

### JsonObject

A collection of static methods for performing operations to objects created via
`JSON_decode`. The objects are assumed to be decoded with `bool $assoc = FALSE`
maintain a difference between key-pair objects and plain indexed arrays.

#### Methods

* `combine` - Consolidate a number of objects into the first passed.
* `merge` - Produces a new object consolidating of all objects passed.
* `get` - Retrieve a value from an objects.
* `set` - Assign a value within the object.
* `remove` - Removes a key:value of an object.
* `exists` - Check that a key exists within the object.
* `loadString` - Performs a json_decode on a string.
* `loadFile` - Performs a json_decode on a file.

#### Exceptions

Some of these methods will throw an `Exception` or `Error` if problems occur.
For simplicity predefined PHP `Throwable` types are used since they do not
require the introduction of bespoke classes. Each has relevant information
contained in their `$messages`.

* `RuntimeException` - When a specified path is not a file.
* `ParseError` - When there is a problem decoding JSON strings.
* `TypeError` - When an assignment failed because it's address passes through a
non-object/array. (i.e. you try to set `foo.bar="baz"` on `{"foo":"bar"}`.)
