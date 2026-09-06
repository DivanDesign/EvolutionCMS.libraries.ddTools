# `\DDTools\Tools\Objects`

Helpers for objects and arrays: property access, type conversion, extend, unfold.

See also:
* [README](../../../README.md)


## Reference


### `\DDTools\Tools\Objects::isPropExists($params)`

* Description: Checks if the object, class or array has a property / element.
	* This is a “syntactic sugar” for checking an element in one way regardless of the “object” type.
	* The first reason for creating this method is convenience to not thinking about type of “object” variables.
	* Second, the different order of parameters in the native PHP functions makes us crazy.
* Modifiers: `public static`


#### Parameters

* `$params`
	* Description: Parameters.
	* Valid values:
		* `stdClass`
		* `arrayAssociative`
	* **Required**
	
* `$params->object`
	* Description: Source object or array.
	* Valid values:
		* `stdClass`
		* `array`
	* **Required**
	
* `$params->propName`
	* Description: Object property name or array key.
	* Valid values:
		* `string`
		* `integer`
	* **Required**


#### Returns

* `$result`
	* Description: `true` if the property exists, `false` otherwise.
	* Valid values: `boolean`


### `\DDTools\Tools\Objects::getPropValue($params)`

* Description: Get the value of an object property or an array element in any nesting level in one way regardless of the “object” type.
* Modifiers: `public static`


#### Parameters

* `$params`
	* Description: Parameters.
	* Valid values:
		* `stdClass`
		* `arrayAssociative`
	* **Required**
	
* `$params->object`
	* Description: Source object or array.
		* It can be nested, and elements of all levels can be mix of objects and arrays (see Examples below).
	* Valid values:
		* `stdClass`
		* `array`
	* **Required**
	
* `$params->propName`
	* Description: Object property name or array key.
		* You can also use `'.'` to get nested properties. Several examples (see also full Examples below):
			* `somePlainProp` — get first-level property
			* `someObjectProp.secondLevelProp` — get property of the `someObjectProp` object|array
			* `someArrayProp.0.thirdLevelProp` — get property of the zero element of the `someArrayProp` array
	* Valid values:
		* `string`
		* `integer`
	* **Required**
	
* `$params->notFoundResult`
	* Description: What will be returned when property is not found.
	* Valid values: `mixed`
	* Default value: `null`


#### Returns

* `$result`
	* Description: Value of an object property or an array element.
	* Valid values:
		* `mixed`
		* `$params->notFoundResult` — if property not exists


### `\DDTools\Tools\Objects::convertType($params)`

* Description: Converts an object type.
	* Arrays, [JSON](https://en.wikipedia.org/wiki/JSON) and [Query string](https://en.wikipedia.org/wiki/Query_string) objects are also supported.
* Modifiers: `public static`


#### Parameters

* `$params`
	* Description: Parameters.
	* Valid values:
		* `stdClass`
		* `arrayAssociative`
	* **Required**
	
* `$params->object`
	* Description: Input object | array | encoded string.
	* Valid values:
		* `stdClass`
		* `object` — custom class instances are also supported
		* `array`
		* `stringJsonObject` — [JSON](https://en.wikipedia.org/wiki/JSON) object
		* `stringJsonArray` — [JSON](https://en.wikipedia.org/wiki/JSON) array
		* `stringHjsonObject` — [HJSON](https://hjson.github.io/) object
		* `stringHjsonArray` — [HJSON](https://hjson.github.io/) array
		* `stringQueryFormatted` — [Query string](https://en.wikipedia.org/wiki/Query_string)
	* **Required**
	
* `$params->type`
	* Description: Type of resulting object.
		* Values are case insensitive (the following names are equal: `'stringjsonauto'`, `'stringJsonAuto'`, `'STRINGJSONAUTO'`, etc).
	* Valid values:
		* `'objectAuto'` — `stdClass` or `array` depends on input object
		* `'objectStdClass'` — `stdClass`
		* `'objectArray'` — `array`
		* `'stringJsonAuto'` — `stringJsonObject` or `stringJsonArray` depends on input object
		* `'stringJsonObject'`
		* `'stringJsonArray'`
		* `'stringQueryFormatted'`
		* `'stringHtmlAttrs'` — HTML attributes string (e. g. `width='100' height='50'`), boolean values will be converted to `0` or `1` (e. g. `data-is-loaded='1'`), objects/arrays will be converted to JSON string (e. g. `data-user-data='{"firstName": "Elon", "lastName": "Musk"}'`)
	* Default value: `'objectAuto'`


#### Returns

* `$result`
	* Description: Result type depends on `$params->type`.
	* Valid values:
		* `stdClass`
		* `array`
		* `stringJsonObject`
		* `stringJsonArray`


### `\DDTools\Tools\Objects::extend($params)`

* Description: Merge the contents of two or more objects or arrays together into the first one.
* Modifiers: `public static`


#### Parameters

* `$params`
	* Description: Parameters.
	* Valid values:
		* `stdClass`
		* `arrayAssociative`
	* **Required**
	
* `$params->objects`
	* Description: Objects or arrays to merge. Moreover, objects can extend arrays and vice versa.
	* Valid values: `array`
	* **Required**
	
* `$params->objects[0]`
	* Description: The object or array to extend. It will receive the new properties.
	* Valid values:
		* `object`
		* `array`
		* `mixed` — if passed something else, the new `stdClass` object will be created instead
	* **Required**
	
* `$params->objects[i]`
	* Description: An object or array containing additional properties to merge in.
	* Valid values:
		* `object`
		* `array`
	* **Required**
	
* `$params->deep`
	* Description: If true, the merge becomes recursive (aka. deep copy).
	* Valid values: `boolean`
	* Default value: `true`
	
* `$params->overwriteWithEmpty`
	* Description: Overwrite fields with empty values (see examples below).
		* The following values are considered to be empty:
			* `''` — an empty string
			* `[]` — an empty array
			* `(object) []` — an empty object
			* `NULL`
	* Valid values: `boolean`
	* Default value: `true`
	
* `$params->extendableProperties`
	* Description: An array of property names that can be extended from additional objects or arrays. Properties in the initial object or array are not restricted by this parameter.  
	* Valid values:
		* `array`
		* `null` or any empty value — all properties will be extended
	* Default value: `null`
	
* `$params->extendableProperties[$i]`
	* Description: The name of a property that is allowed to be extended from additional objects or arrays.  
	* Valid values: `string`
	* **Required**


### `\DDTools\Tools\Objects::unfold($params)`

* Description: Converts a multidimensional array/object into an one-dimensional one joining the keys with `$params->keySeparator`.
	* For example, it can be helpful while using placeholders like `[+size.width+]`.
* Modifiers: `public static`


#### Parameters

* `$params`
	* Description: Parameters.
	* Valid values:
		* `stdClass`
		* `arrayAssociative`
	* **Required**
	
* `$params->object`
	* Description: An object/array to convert.
	* Valid values:
		* `stdClass`
		* `arrayAssociative`
	* **Required**
	
* `$params->keySeparator`
	* Description: Separator between nested keys in the result object/array.
	* Valid values: `string`
	* Default value: `'.'`
	
* `$params->keyPrefix`
	* Description: Prefix of the keys of an object/array (it's an internal varible, but can be used if required).
	* Valid values: `string`
	* Default value: `''`
	
* `$params->isCrossTypeEnabled`
	* Description: This parameter determines whether the method should process elements across different data types, such as arrays and objects, at all levels. When set to `true`, the method will recursively unfold elements of both array and object types, regardless of the type of the root parent.
	* Valid values: `boolean`
	* Default value: `false`


#### Returns

* `$result`
	* Description: Unfolded object/array. Type of results depends on `$params->object`.
	* Valid values:
		* `stdClass`
		* `array`


## Examples


### `\DDTools\Tools\Objects::convertType($params)`


#### Convert a JSON or Query encoded string to an array

For example, some snippet supports 2 formats in one of parameters: JSON or Query string.
Users use the format that is convenient to them and we support both.
Just call this method and don't care about it.

```php
// We can pass string in JSON format
\DDTools\Tools\Objects::convertType([
	'object' => '{
		"pagetitle": "Test title",
		"published": "0"
	}',
	'type' => 'objectArray',
]);

// Or Query string
\DDTools\Tools\Objects::convertType([
	'object' => 'pagetitle=Test title&published=0',
	'type' => 'objectArray',
]);
```

Both calls return:

```php
[
	'pagetitle' => 'Test title',
	'published' => '0',
];
```


#### Convert a Query encoded string to a JSON object string

```php
\DDTools\Tools\Objects::convertType([
	'object' => 'firstName=Hans&lastName=Zimmer',
	'type' => 'stringJsonAuto',
]);
```

Returns:

```json
{
	"firstName": "Hans",
	"lastName": "Zimmer"
}
```


#### Convert a JSON object to a JSON array

```php
\DDTools\Tools\Objects::convertType([
	'object' => '{
		"firstName": "Ramin",
		"lastName": "Djawadi"
	}',
	'type' => 'stringJsonArray',
]);
```

Returns:

```json
[
	"Ramin",
	"Djawadi"
]
```


#### Convert a HJSON encoded string to an object

```php
\DDTools\Tools\Objects::convertType([
	'object' => "{
		// This is HJSON, not JSON, so we can use comments insides
		keys: and values can be specified without quotes,
		multilineValues:
			'''
			Write multiline strings with proper whitespace handling.
			Starts and ends with triple quotes.
			A simple syntax and easy to read.
			'''
	}",
	'type' => 'objectStdClass',
]);
```

Returns:

```php
stdClass::__set_state(array(
   'keys' => 'and values can be specified without quotes,',
   'multilineValues' => 'Write multiline strings with proper whitespace handling.
		Starts and ends with triple quotes.
		A simple syntax and easy to read.'
	,
))
```


#### Convert an associative array to a string of HTML attributes

```php
\DDTools\Tools\Objects::convertType([
	'object' => [
		'data-name' => 'KINO',
		// Will be converted to 1
		'data-is-active' => true,
		// Will be converted to JSON array
		'data-members' => [
			'Viktor Tsoi',
			'Yuri Kasparyan',
			'Aleksei Rybin',
			'Igor Tikhomirov',
			'Aleksandr Titov',
			'Georgy Guryanov',
			'Oleg Valinsky',
		],
	],
	'type' => 'stringHtmlAttrs',
]);
```

Returns:

```html
data-name='KINO' data-is-active='1' data-members='["Viktor Tsoi","Yuri Kasparyan","Aleksei Rybin","Igor Tikhomirov","Aleksandr Titov","Georgy Guryanov","Oleg Valinsky"]'
```


### `\DDTools\Tools\Objects::extend($params)`


#### Merge two objects, modifying the first

```php
var_export(
	\DDTools\Tools\Objects::extend([
		'objects' => [
			(object) [
				'cat' => 'mew',
				'dog' => (object) [
					'name' => 'Floyd',
					'weight' => 6,
				],
				'rabbit' => 42,
			],
			(object) [
				'dog' => (object) [
					'weight' => 10,
				],
				'bird' => 0,
			],
		],
	])
);
```

Returns:

```php
stdClass::__set_state(array(
	'cat' => 'mew',
	'dog' => stdClass::__set_state(array(
		'name' => 'Floyd',
		'weight' => 10,
	)),
	'rabbit' => 42,
	'bird' => 0,
))
```


#### Also you can extend arrays

```php
var_export(
	\DDTools\Tools\Objects::extend([
		'objects' => [
			[
				'cat' => 'mew',
				'dog' => [
					'name' => 'Floyd',
					'weight' => 6,
				],
				'rabbit' => 42,
			],
			[
				'dog' => (object) [
					'weight' => 10,
				],
				'bird' => 0,
			],
		],
	])
);
```

Returns:

```php
array(
	'cat' => 'mew',
	'dog' => array(
		'name' => 'Floyd',
		'weight' => 10,
	),
	'rabbit' => 42,
	'bird' => 0,
)
```


#### Moreover, objects can extend arrays and vice versa

```php
var_export(
	\DDTools\Tools\Objects::extend([
		'objects' => [
			[
				'name' => 'jokes',
				'countries' => (object) [
					'usa' => 'democracy',
					'china' => 'chinese democracy',
				],
			],
			(object) [
				'countries' => [
					'china' => 'democracy too',
				],
			],
		],
	])
);
```

Returns:

```php
// The object expanded the source array
array(
	'name' => 'jokes',
	// The array expanded the source object
	'countries' => stdClass::__set_state(
		'usa' => 'democracy',
		'china' => 'democracy too',
	)),
)
```


#### Don't overwrite fields with empty values (`$params->overwriteWithEmpty` == `false`)

By default, empty field values (e. g. `''`) are treated as other values and will replace non-empty ones.

```php
var_export(
	\DDTools\Tools\Objects::extend([
		'objects' => [
			(object) [
				'firstName' => 'John',
				'lastName' => 'Tesla',
				'discipline' => 'Electrical engineering',
			],
			(object) [
				'firstName' => 'Nikola',
				'lastName' => '',
			],
		],
	])
);
```

Returns:

```php
stdClass::__set_state(array(
	'firstName' => 'Nikola',
	'lastName' => '',
	'discipline' => 'Electrical engineering',
))
```

Empty `lastName` from the second object replaced non-empty `lastName` from the first.

If you want to ignore empty values, just use `$params->overwriteWithEmpty` == `false`:

```php
var_export(
	\DDTools\Tools\Objects::extend([
		'objects' => [
			(object) [
				'firstName' => 'John',
				'lastName' => 'Tesla',
				'discipline' => 'Electrical engineering',
			],
			(object) [
				'firstName' => 'Nikola',
				'lastName' => '',
			],
		],
		'overwriteWithEmpty' => false,
	])
);
```

Returns:

```php
stdClass::__set_state(array(
	'firstName' => 'Nikola',
	'lastName' => 'Tesla',
	'discipline' => 'Electrical engineering',
))
```


#### Extending only specific properties from subsequent objects (`$params->extendableProperties`)

Sometimes you want to keep only the key ingredients, like avoiding the pineapple on your pizza.

```php
var_export(
	\DDTools\Tools\Objects::extend([
		'objects' => [
			(object) [
				'name' => 'Classic Italian Pizza',
				'toppings' => (object) [
					'cheese' => 'mozzarella',
					'tomatoSauce' => true,
					'olive' => true,
				],
				'size' => 'medium',
			],
			[
				// Not interested in extra toppings
				'toppings' => [
					'pineapple' => true,
				],
				'size' => 'large',
				'price' => 15.99,
			],
		],
		// Only keeping the price and size
		'extendableProperties' => [
			'price',
			'size',
		],
	])
);
```

Returns:

```php
stdClass::__set_state(array(
	'name' => 'Classic Italian Pizza',
	'toppings' => stdClass::__set_state(array(
		'cheese' => 'mozzarella',
		'tomatoSauce' => true,
		'olive' => true,
	)),
	'size' => 'large',
	'price' => 15.99,
))
```


### `\DDTools\Tools\Objects::unfold($params)`


#### Unfold an object

```php
var_export(
	\DDTools\Tools\Objects::unfold([
		'object' => (object) [
			'name' => 'Elon Musk',
			'address' => (object) [
				'line1' => '3500 Deer Creek Road',
				'city' => 'Palo Alto',
				'state' => 'California',
				'country' => 'United States',
			],
		],
	])
);
```

Returns:

```php
stdClass::__set_state(array (
	'name' => 'Elon Musk',
	'address.line1' => '3500 Deer Creek Road',
	'address.city' => 'Palo Alto',
	'address.state' => 'California',
	'address.country' => 'United States',
))
```


#### Unfold an array

```php
var_export(
	\DDTools\Tools\Objects::unfold([
		'object' => [
			'a' => 'a val',
			'b' => [
				'b1' => 'b1 val',
				'b2' => [
					'b21' => 'b21 val',
					'b22' => 'b22 val',
				],
			],
			'c' => 'c val',
		],
	])
);
```

Returns:

```php
array (
	'a' => 'a val',
	'b.b1' => 'b1 val',
	'b.b2.b21' => 'b21 val',
	'b.b2.b22' => 'b22 val',
	'c' => 'c val',
)
```


#### Use custom key separator

```php
var_export(
	\DDTools\Tools\Objects::unfold([
		'object' => [
			'name' => 'Elon Musk',
			'parents' => [
				'mother' => 'Maye Musk',
				'father' => 'Errol Musk',
			],
		],
		'keySeparator' => '_',
	])
);
```

Returns:

```php
stdClass::__set_state(array (
	'name' => 'Elon Musk',
	'parents_mother' => 'Maye Musk',
	'parents_father' => 'Errol Musk',
))
```


#### Cross-type unfolding (`$params->isCrossTypeEnabled` == `true`)

```php
// Array
$data = [
	// Array
	'bin1' => [
		'plastic' => 'plastic bottles',
		'paper' => 'newspapers',
		'glass' => 'glass bottles',
	],
	// Object
	'bin2' => (object) [
		'organic' => 'food waste',
		'paper' => 'cardboard boxes',
		'metal' => 'aluminum cans',
	],
];
```

##### Without cross-type unfolding (by default)

```php
var_export(
	\DDTools\Tools\Objects::unfold([
		'object' => $data,
	])
);
```

Returns:

```php
array (
	'bin1.plastic' => 'plastic bottles',
	'bin1.paper' => 'newspapers',
	'bin1.glass' => 'glass bottles',
	'bin2' => (object) array(
		'organic' => 'food waste',
		'paper' => 'cardboard boxes',
		'metal' => 'aluminum cans',
	),
)
```

##### With cross-type unfolding enabled

```php
var_export(
	\DDTools\Tools\Objects::unfold([
		'object' => $data,
		'isCrossTypeEnabled' => true,
	])
);
```

Returns:

```php
array (
	'bin1.plastic' => 'plastic bottles',
	'bin1.paper' => 'newspapers',
	'bin1.glass' => 'glass bottles',
	'bin2.organic' => 'food waste',
	'bin2.paper' => 'cardboard boxes',
	'bin2.metal' => 'aluminum cans',
)
```


### `\DDTools\Tools\Objects::isPropExists($params)`

Checks if the object, class or array has a property / element using the same syntax.

You can pass an object:

```php
var_export(
	\DDTools\Tools\Objects::isPropExists([
		'object' => (object) [
			'firstName' => 'John',
			'lastName' => 'Lennon',
		],
		'propName' => 'firstName',
	])
);
```

Or an array:

```php
var_export(
	\DDTools\Tools\Objects::isPropExists([
		'object' => [
			'firstName' => 'Paul',
			'lastName' => 'McCartney',
		],
		'propName' => 'firstName',
	])
);
```

Both calls return `true`.


### `\DDTools\Tools\Objects::getPropValue($params)`


#### Get the value of an object property or an array element using the same syntax

You can pass an object:

```php
var_export(
	\DDTools\Tools\Objects::getPropValue([
		'object' => (object) [
			'name' => 'Floyd',
			'weight' => 7,
		],
		'propName' => 'name',
	])
);
```

Or an array:

```php
var_export(
	\DDTools\Tools\Objects::getPropValue([
		'object' => [
			'name' => 'Floyd',
			'weight' => 7,
		],
		'propName' => 'name',
	])
);
```

Both calls return `'Floyd'`.


#### Get the value of an object property or an array element in any nesting level

Source object can be nested, and elements of all levels can be mix of objects and arrays.

```php
// For example let the first level be stdClass
$sourceObject = (object) [
	// Let the second level be an indexed array
	'PinkFloyd' => [
		// Let the third level be an associative array
		[
			'name' => 'Syd Barrett',
			'role' => 'lead and rhythm guitars, vocals',
		],
		[
			'name' => 'David Gilmour',
			'role' => 'lead and rhythm guitars, vocals, bass, keyboards, synthesisers',
		],
		// Let Roger be a little bit special ;)
		(object) [
			'name' => 'Roger Waters',
			'role' => 'bass, vocals, rhythm guitar, synthesisers',
		],
		[
			'name' => 'Richard Wright',
			'role' => 'keyboards, piano, organ, synthesisers, vocals',
		],
		[
			'name' => 'Nick Mason',
			'role' => 'drums, percussion',
		],
	],
];
```


##### Get a first-level property

There's nothing special, just look at this example for the full picture.

```php
var_export(
	\DDTools\Tools\Objects::getPropValue([
		'object' => $sourceObject,
		'propName' => 'PinkFloyd',
	])
);
```

Returns:

```php
array (
	0 => array (
		'name' => 'Syd Barrett',
		'role' => 'lead and rhythm guitars, vocals',
	),
	1 => array (
		'name' => 'David Gilmour',
		'role' => 'lead and rhythm guitars, vocals, bass, keyboards, synthesisers',
	),
	2 => stdClass::__set_state(array(
		 'name' => 'Roger Waters',
		 'role' => 'bass, vocals, rhythm guitar, synthesisers',
	)),
	3 => array (
		'name' => 'Richard Wright',
		'role' => 'keyboards, piano, organ, synthesisers, vocals',
	),
	4 => array (
		'name' => 'Nick Mason',
		'role' => 'drums, percussion',
	),
)
```


##### Get a second-level property

Let's make it a little more interesting: let's get the 4th element of the second-level indexed array.

```php
var_export(
	\DDTools\Tools\Objects::getPropValue([
		'object' => $sourceObject,
		'propName' => 'PinkFloyd.4',
	])
);
```

Returns:

```php
array (
	'name' => 'Nick Mason',
	'role' => 'drums, percussion',
)
```


##### Get a third-level property

Any level of nesting is supported.

No matter what type of element is used in any nesting level, the method will work fine.
So let's get Roger's name. As you remember, he is stdClass as opposed to the other members who are associative arrays.

```php
var_export(
	\DDTools\Tools\Objects::getPropValue([
		'object' => $sourceObject,
		'propName' => 'PinkFloyd.2.name',
	])
);
```

Returns:

```php
'Roger Waters'
```


##### Of course, it works fine with single-level objects that contain `'.'` in their property names

```php
var_export(
	\DDTools\Tools\Objects::getPropValue([
		'object' => [
			'1973.03.01' => 'The Dark Side of the Moon',
			'1975.09.12' => 'Wish You Were Here',
		],
		'propName' => '1975.09.12',
	])
);
```

Returns:

```php
'Wish You Were Here'
```


<link rel="stylesheet" type="text/css" href="https://raw.githack.com/DivanDesign/CSS.ddMarkdown/master/style.min.css" />
