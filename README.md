# PHP Utils

A collection of PHP classes which offer general convenience for common tasks
which are not supported out-of-the box by the language.

## Classes

### JsonObject

A collection of static methods for performing operations to objects created via
`json_decode`. The objects are assumed to be decoded with `bool $assoc = FALSE`
maintain a difference between key-pair objects and plain indexed arrays.

The operations made availabe though this class are;

* `combine` - Consolidate a number of objects into the first passed.
* `merge` - Produces a new object consolidating of all objects passed.
* `get` - Retrieve a value from an objects.
* `set` - Assign a value within the object.
* `remove` - Removes a key:value of an object.
* `exists` - Check that a key exists within the object.
