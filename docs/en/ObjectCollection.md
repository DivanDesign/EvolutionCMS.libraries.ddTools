# `\DDTools\ObjectCollection`

Class representing a collection of some objects or arrays.

See also:
* [README](../../README.md)


## Reference


### `\DDTools\ObjectCollection::__construct($params)`


#### Parameters

* `$params`
	* Description: Parameters.
	* Valid values:
		* `stdClass`
		* `arrayAssociative`
	* Default value: —
	
* `$params->items`
	* Description: An array of items.
		* You can avoid this parameter to create an empty collection and set items later.
	* Valid values:
		* `array` — can be indexed or associative, keys will not be used
		* `object` — also can be set as an object for better convenience, only property values will be used
		* `stringJsonObject` — [JSON](https://en.wikipedia.org/wiki/JSON) object
		* `stringJsonArray` — [JSON](https://en.wikipedia.org/wiki/JSON) array
		* `stringHjsonObject` — [HJSON](https://hjson.github.io/) object
		* `stringHjsonArray` — [HJSON](https://hjson.github.io/) array
		* `stringQueryFormatted` — [Query string](https://en.wikipedia.org/wiki/Query_string)
	* Default value: —
	
* `$params->items[$itemIndex]`
	* Description: An item.
	* Valid values:
		* `array` — indexed arrays are supported as well as associative
		* `object`
	* **Required**
	
* `$params->itemType`
	* Description: Allows to convert item type. If set, each item of `$params->items` will be converted to needed type.
		* Values are case insensitive (the following names are equal: `'objectstdclass'`, `'objectStdClass'`, `'OBJECTSTDCLASS'`, etc).
	* Valid values:
		* `'objectStdClass'`
		* `'objectArray'`
		* `null` — do not convert type of items, use them as is
	* Default value: `null`


### `\DDTools\ObjectCollection::setItems($params)`

* Description: Sets new collection items. Existing items will be removed.
	* Has the same parameters as `\DDTools\ObjectCollection::__construct($params)`.
* Modifiers: `public instance`


### `\DDTools\ObjectCollection::addItems($params)`

* Description: Appends items onto the end of collection.
	* Has the same parameters as `\DDTools\ObjectCollection::__construct($params)`.
* Modifiers: `public instance`


### `\DDTools\ObjectCollection::getItems($params)`

* Description: Gets an array of required collection items.
* Modifiers: `public instance`

#### Parameters

* `$params`
	* Description: Parameters.
	* Valid values:
		* `stdClass`
		* `arrayAssociative`
	* Default value: —
	
* `$params->filter`
	* Description: Filter clause for item properties.
		* Thus,
			```
			'
			"gender" == "female"
			|| "gender" == "male"
			&& "firstName" != "Bill"
			&& "lastName"
			'
			```
			returns:
			* All items with the `gender` property equal to `'female'`.
			* All items:
				* with the `gender` property equal to `'male'` **and**
				* with the `firstName` property not equal to `'Bill'` **and**
				* with the `lastName` property is exist with any value.
		* Quoted property names and values are optional, this is valid too:
			```
			'
			gender == female
			|| gender == male
			&& firstName != Bill
			&& lastName
			' 
			```
		* Single quotes are also supported as double quotes:
			```
			"
			gender == 'a'
			|| gender == 'b'
			&& firstName != 'Bill'
			&& lastName
			"
			```
		* Spaces, tabs and line breaks are optional, this is valid too: `gender==female||gender==male&&firstName!=Bill&&lastName`.
	* Valid values: `stringSeparated`
	* Default value: `''` (without filtration)
	
* `$params->limit`
	* Description: Maximum number of items to return.
	* Valid values:
		* `integer`
		* `0` — all matching items
	* Default value: `0`
	
* `$params->propAsResultKey`
	* Description: Item property, which value will be an item key in result array instead of an item index.
		* For example, it can be useful if items have an ID property or something like that.
	* Valid values:
		* `string`
		* `null` — result array will be indexed
	* Default value: `null`
	
* `$params->propAsResultValue`
	* Description: Item property, which value will be an item value in result array instead of an item object.
	* Valid values:
		* `string`
		* `null` — result array values will item objects
	* Default value: `null`


#### Returns

* `$result`
	* Description: An array of items.
	* Valid values:
		* `arrayIndexed`
		* `arrayAssociative` — item property values will be used as result keys if `$params->propAsResultKey` is set
	
* `$result[$itemIndex|$itemFieldValue]`
	* Description: An item object or item property value if specified in `$params->propAsResultValue`.  
	* Valid values: `mixed`


### `\DDTools\ObjectCollection::getOneItem($params)`

* Description: Gets required item.
* Modifiers: `public instance`

#### Parameters

* `$params`
	* Description: Parameters.
	* Valid values:
		* `stdClass`
		* `arrayAssociative`
	* Default value: —
	
* `$params->filter`
	* Description: Filter clause for item properties. The same parameter as `\DDTools\ObjectCollection::getItems($params)`.
	* Valid values: `stringSeparated`
	* Default value: `''` (first found item will be returned)
	
* `$params->notFoundResult`
	* Description: What will be returned when no items found.
	* Valid values: `mixed`
	* Default value: `null`


#### `\DDTools\ObjectCollection::count()`

* Description: Counts all items.
* Modifiers: `public instance`


#### `\DDTools\ObjectCollection::convertItemsType($params)`

* Description: Converts type of needed items in collection.
* Modifiers: `public instance`

##### Parameters

* `$params`
	* Description: Parameters.
	* Valid values:
		* `stdClass`
		* `arrayAssociative`
	* Default value: —
	
* `$params->itemType`
	* Description: Result item type.  
		Values are case insensitive (the following names are equal: `'objectstdclass'`, `'objectStdClass'`, `'OBJECTSTDCLASS'`, etc).
	* Valid values:
		* `'objectStdClass'`
		* `'objectArray'`
	* **Required**
	
* `$params->filter`
	* Description: Filter clause for item properties. The same parameter as `\DDTools\ObjectCollection::getItems($params)`.
	* Valid values: `stringSeparated`
	* Default value: `''` (all items will be converted)


#### `\DDTools\ObjectCollection::updateItems($params)`

* Description: Updates properties of existing items with new values.
* Modifiers: `public instance`

##### Parameters

* `$params`
	* Description: Parameters.
	* Valid values:
		* `stdClass`
		* `arrayAssociative`
	* **Required**
	
* `$params->data`
	* Description: New item data. Existing item will be extended by this data, it means:
		* Type of existing item will not be changed.
		* All given property values will overwrite existing.
		* Non-existing given properties will be created.
		* Existing properties that absent in `$params->data` will remain as is.
	* Valid values:
		* `array`
		* `object`
	* **Required**
	
* `$params->filter`
	* Description: Filter clause for item properties. The same parameter as `\DDTools\ObjectCollection::getItems($params)`.
	* Valid values: `stringSeparated`
	* Default value: `''` (any items will be updated)
	
* `$params->limit`
	* Description: Maximum number of items can be updated.
	* Valid values:
		* `integer`
		* `0` — all matching items
	* Default value: `0`


#### `\DDTools\ObjectCollection::deleteItems($params)`

* Description: Deletes required items from collection.
* Modifiers: `public instance`

##### Parameters

* `$params`
	* Description: Parameters.
	* Valid values:
		* `stdClass`
		* `arrayAssociative`
	* Default value: —
	
* `$params->filter`
	* Description: Filter clause for item properties. The same parameter as `\DDTools\ObjectCollection::getItems($params)`.
	* Valid values: `stringSeparated`
	* Default value: `''` (any items will be deleted)
	
* `$params->limit`
	* Description: Maximum number of items can be deleted.
	* Valid values:
		* `integer`
		* `0` — all matching items
	* Default value: `0`


#### `\DDTools\ObjectCollection::toJSON()`, `\DDTools\ObjectCollection::__toString()`

* Description: Gets an JSON-array of all collection items.
* Modifiers: `public instance`


##### Returns

* `$result`
	* Description: An JSON-array of items.
	* Valid values: `stringJsonArray`


#### `\DDTools\ObjectCollection::setOneItemData($params)`

* Description: Sets data of an item object. All setting of an item data inside the class must use this method.
	It's convenient to override this method in child classes if items are not plain objects.
* Modifiers: `protected instance`

##### Parameters

* `$params`
	* Description: Parameters.
	* Valid values:
		* `stdClass`
		* `arrayAssociative`
	* **Required**
	
* `$params->index`
	* Description: Item index which data will be set.
	* Valid values: `integer`
	* **Required**
	
* `$params->data`
	* Description: New item data.
	* Valid values:
		* `array` — indexed arrays are supported as well as associative
		* `object`
	* **Required**


#### `\DDTools\ObjectCollection::getOneItemData($params)`

* Description: Returns data of an item object. All getting of an item data inside the class must use this method.
	It's convenient to override this method in child classes if items are not plain objects.
* Modifiers: `protected instance`

##### Parameters

* `$params`
	* Description: Parameters.
	* Valid values:
		* `stdClass`
		* `arrayAssociative`
	* **Required**
	
* `$params->itemObject`
	* Description: An item object which data will be returned.
	* Valid values:
		* `array` — indexed arrays are supported as well as associative
		* `object`
	* **Required**


##### Returns

* `$result`
	* Description: Data of an item object.
	* Valid values:
		* `array`
		* `object`


## Examples


### Create a collection with items

```php
$collection = new \DDTools\ObjectCollection([
	'items' => [
		[
			'name' => 'Mary Teresa',
			'isHuman' => 1,
			'gender' => 'female',
			'nobelPeacePrize' => 1,
			'religion' => 'Catholicism',
		],
		[
			'name' => 'Mahatma Gandhi',
			'isHuman' => 1,
			'gender' => 'male',
			'nobelPeacePrize' => 0,
		],
		[
			'name' => 'Tenzin Gyatso',
			'isHuman' => 1,
			'gender' => 'male',
			'nobelPeacePrize' => 1,
			'religion' => 'Tibetan Buddhism',
		],
		[
			'name' => 'ICAN',
			'isHuman' => 0,
			'nobelPeacePrize' => 1,
		],
	],
]);
```


### Set items as a JSON string

```php
$collection->setItems([
	'items' => '[
		{
			"name": "Mary Teresa",
			"isHuman": 1,
			"gender": "female",
			"nobelPeacePrize": 1,
			"religion": "Catholicism"
		},
		{
			"name": "Mahatma Gandhi",
			"isHuman": 1,
			"gender": "male",
			"nobelPeacePrize": 0
		},
		{
			"name": "Tenzin Gyatso",
			"isHuman": 1,
			"gender": "male",
			"nobelPeacePrize": 1,
			"religion": "Tibetan Buddhism"
		},
		{
			"name": "ICAN",
			"isHuman": 0,
			"nobelPeacePrize": 1
		}
	]'
]);
```


### `\DDTools\ObjectCollection::getItems($params)`


#### Get an array of items using filter (`$params->filter`)


##### Filter by existence of a property

```php
$collection->getItems([
	'filter' => 'religion',
]);
```

Returns:

```php
array(
	0 => array(
		'name' => 'Mary Teresa',
		'isHuman' => 1,
		'gender' => 'female',
		'nobelPeacePrize' => 1,
		'religion' => 'Catholicism',
	),
	1 => array(
		'name' => 'Tenzin Gyatso',
		'isHuman' => 1,
		'gender' => 'male',
		'nobelPeacePrize' => 1,
		'religion' => 'Tibetan Buddhism',
	),
)
```


##### Filter by a property value

```php
$collection->getItems([
	'filter' => 'gender==male',
]);
```

Returns:

```php
array(
	0 => array(
		'name' => 'Mahatma Gandhi',
		'isHuman' => 1,
		'gender' => 'male',
		'nobelPeacePrize' => 0,
	),
	1 => array(
		'name' => 'Tenzin Gyatso',
		'isHuman' => 1,
		'gender' => 'male',
		'nobelPeacePrize' => 1,
		'religion' => 'Tibetan Buddhism',
	),
)
```


##### Filter using several conditions

```php
$collection->getItems([
	// Spaces, tabs and line breaks are also allowed and do not matter
	'filter' => '
		gender == female
		|| nobelPeacePrize == 1 && isHuman == 0
	'
]);
```

Returns:

```php
array(
	// gender == female
	0 => array(
		'name' => 'Mary Teresa',
		'isHuman' => 1,
		'gender' => 'female',
		'nobelPeacePrize' => 1,
		'religion' => 'Catholicism',
	),
	// nobelPeacePrize == 1 && isHuman == 0
	1 => array(
		'name' => 'ICAN',
		'isHuman' => 0,
		'nobelPeacePrize' => 1,
	),
)
```


#### Get an associative array of items using a property value as a result key

```php
$collection->getItems([
	'propAsResultKey' => 'name',
]);
```

Returns:

```php
array(
	'Mary Teresa' => array(
		'name' => 'Mary Teresa',
		'isHuman' => 1,
		'gender' => 'female',
		'nobelPeacePrize' => 1,
		'religion' => 'Catholicism',
	),
	'Mahatma Gandhi' => array(
		'name' => 'Mahatma Gandhi',
		'isHuman' => 1,
		'gender' => 'male',
		'nobelPeacePrize' => 0,
	),
	'Tenzin Gyatso' => array(
		'name' => 'Tenzin Gyatso',
		'isHuman' => 1,
		'gender' => 'male',
		'nobelPeacePrize' => 1,
		'religion' => 'Tibetan Buddhism',
	),
	'ICAN' => array(
		'name' => 'ICAN',
		'isHuman' => 0,
		'nobelPeacePrize' => 1,
	),
)
```


#### Get a one-dimensional array of item property values

```php
$collection->getItems([
	'propAsResultKey' => 'name',
	'propAsResultValue' => 'isHuman',
]);
```

Returns:

```php
array(
	'Mary Teresa' => 1,
	'Mahatma Gandhi' => 1,
	'Tenzin Gyatso' => 1,
	'ICAN' => 0,
)
```


### `\DDTools\ObjectCollection::getOneItem($params)`

```php
$collection->getOneItem([
	'filter' => 'name == Mahatma Gandhi',
]);
```

Returns:

```php
array(
	'name' => 'Mahatma Gandhi',
	'isHuman' => 1,
	'gender' => 'male',
	'nobelPeacePrize' => 0,
)
```


#### Custom results when no items found


```php
$collection->getOneItem([
	'filter' => 'name == European Union',
	'notFoundResult' => [
		'name' => 'Default item',
		'nobelPeacePrize' => 0,
	],
]);
```

Returns:

```php
array(
	'name' => 'Default item',
	'nobelPeacePrize' => 0,
)
```


### `\DDTools\ObjectCollection::convertItemsType($params)`

```php
$collection->convertItemsType([
	'filter' => 'gender==male',
	'itemType' => 'objectStdClass',
]);

$collection->getItems();
```

Returns:

```php
array(
	0 => array(
		'name' => 'Mary Teresa',
		'isHuman' => 1,
		'gender' => 'female',
		'nobelPeacePrize' => 1,
		'religion' => 'Catholicism',
	),
	1 => stdClass::__set_state(array(
		'name' => 'Mahatma Gandhi',
		'isHuman' => 1,
		'gender' => 'male',
		'nobelPeacePrize' => 0,
	)),
	2 => stdClass::__set_state(array(
		'name' => 'Tenzin Gyatso',
		'isHuman' => 1,
		'gender' => 'male',
		'nobelPeacePrize' => 1,
		'religion' => 'Tibetan Buddhism',
	)),
	3 => array(
		'name' => 'ICAN',
		'isHuman' => 0,
		'nobelPeacePrize' => 1,
	),
)
```


### `\DDTools\ObjectCollection::updateItems($params)`

```php
$collection->updateItems([
	'filter' => 'name==Mahatma Gandhi',
	'data' => [
		'nobelPeacePrize' => 1,
		'birthday' => '2 October 1869',
	]
]);

$collection->getItems(
	'filter' => 'name==Mahatma Gandhi',
);
```

Returns:

```php
array(
	0 => stdClass::__set_state(array(
		// Existing properties that absent in `$params->data` have remained as is
		'name' => 'Mahatma Gandhi',
		'isHuman' => 1,
		'gender' => 'male',
		// Given property values have overwritten the existing ones
		'nobelPeacePrize' => 1,
		// Non-existing properties have been created
		'birthday' => '2 October 1869',
	))
)
```


### `\DDTools\ObjectCollection::deleteItems($params)`

```php
$collection->updateItems([
	'filter' => 'isHuman==1',
	'limit' => 2,
]);

$collection->getItems();
```

Returns:

```php
array(
	// 2 humans have been deleted, 1 have remained
	0 => stdClass::__set_state(array(
		'name' => 'Tenzin Gyatso',
		'isHuman' => 1,
		'gender' => 'male',
		'nobelPeacePrize' => 1,
		'religion' => 'Tibetan Buddhism',
	)),
	1 => array(
		'name' => 'ICAN',
		'isHuman' => 0,
		'nobelPeacePrize' => 1,
	),
)
```


<link rel="stylesheet" type="text/css" href="https://raw.githack.com/DivanDesign/CSS.ddMarkdown/master/style.min.css" />
