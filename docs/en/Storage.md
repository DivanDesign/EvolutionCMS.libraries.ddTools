# `\DDTools\Storage`

A uniform API for collections of items: add, update, delete, get.
Use it when a snippet or library needs its own table, or when you want to read and write an existing DB table (e. g. `site_content`) without raw SQL queries.

`\DDTools\Storage\Storage` is the abstract contract.
`\DDTools\Storage\DB\Storage` stores items in a database table: it creates the table if it does not exist and adds missing columns on construct.

See also:
* [README](../../README.md)


## Reference


### `\DDTools\Storage\Storage`

Abstract class for item storage.
Implementations define how items are stored (e. g. `\DDTools\Storage\DB\Storage` for database tables).

Uses `\DDTools\Base\AncestorTrait`.


#### `\DDTools\Storage\Storage::items_add($params)`

* Description: Adds items.
* Modifiers: `abstract public instance`

##### Parameters

* `$params`
	* Description: Parameters.
	* Valid values:
		* `stdClass`
		* `arrayAssociative`
	* **Required**
	
* `$params->items`
	* Description: An array of items.
	* Valid values:
		* `array` — can be indexed or associative, keys will not be used
		* `object` — also can be set as an object for better convenience, only property values will be used
		* `stringJsonObject` — [JSON](https://en.wikipedia.org/wiki/JSON) object
		* `stringJsonArray` — [JSON](https://en.wikipedia.org/wiki/JSON) array
		* `stringHjsonObject` — [HJSON](https://hjson.github.io/) object
		* `stringHjsonArray` — [HJSON](https://hjson.github.io/) array
		* `stringQueryFormatted` — [Query string](https://en.wikipedia.org/wiki/Query_string)
	* **Required**
	
* `$params->items[$itemIndex]`
	* Description: An item.
	* Valid values:
		* `object`
		* `array`
	* **Required**
	
* `$params->items[$itemIndex]->{$propName}`
	* Description: Keys are property names, values are values.
		* Only valid property names will be used, others will be ignored.
	* Valid values: `mixed`
	* **Required**


##### Returns

* `$result`
	* Description: An array of added items.
	* Valid values: `arrayIndexed`
	
* `$result[$itemIndex]`
	* Description: An item object.
	* Valid values: `stdClass`
	
* `$result[$itemIndex]->id`
	* Description: ID of added item.
	* Valid values: `integer`


#### `\DDTools\Storage\Storage::items_addOne($params)`

* Description: Adds a single item.
* Modifiers: `public instance`


##### Parameters

* `$params`
	* Description: Parameters.
	* Valid values:
		* `stdClass`
		* `arrayAssociative`
	* **Required**
	
* `$params->data`
	* Description: An item.
	* Valid values:
		* `object`
		* `array`
	* **Required**
	
* `$params->data->{$propName}`
	* Description: Keys are property names, values are values.
		* Only valid property names will be used, others will be ignored.
	* Valid values: `mixed`
	* **Required**


##### Returns

* `$result`
	* Description: An added item object or `null` if fail.
	* Valid values:
		* `stdClass`
		* `null`
	
* `$result->id`
	* Description: ID of added item (if ID is used).
	* Valid values: `integer`


#### `\DDTools\Storage\Storage::items_update($params)`

* Description: Updates existing items. Existing item data will be extended by `$params->data`.
* Modifiers: `abstract public instance`


##### Parameters

* `$params`
	* Description: Parameters.
	* Valid values:
		* `stdClass`
		* `arrayAssociative`
	* **Required**
	
* `$params->data`
	* Description: New item data. Existing item will be extended by this data.
	* Valid values:
		* `object`
		* `array`
	* **Required**
	
* `$params->data->{$propName}`
	* Description: Keys are property names, values are values.
		* Only valid property names will be used, others will be ignored.
	* Valid values: `mixed`
	* **Required**
	
* `$params->where`
	* Description: SQL `WHERE` clause.
		* Only valid property names will be used, others will be ignored.
	* Valid values:
		* `stdClass`
		* `arrayAssociative`
		* `string` — raw SQL `WHERE` clause
		* `null` || `''` — all items will be updated
	* Default value: `''`
	
* `$params->where->{$propName}`
	* Description: Key is an item property name, value is a value.
		* You can specify multiple value variants through an array (SQL `IN()` operator), please note that empty arrays will just be ignored.
	* Valid values:
		* `string`
		* `arrayIndexed`
	* **Required**
	
* `$params->where->{$propName}[$i]`
	* Description: A value.
	* Valid values: `string`
	* **Required**
	
* `$params->limit`
	* Description: Maximum number of items to update.
	* Valid values:
		* `integer`
		* `0` — all matching items
	* Default value: `0`
	
* `$params->offset`
	* Description: Offset of the first item (can be useful with `$params->limit`).
	* Valid values: `integer`
	* Default value: `0`


##### Returns

* `$result`
	* Description: An array of updated items.
	* Valid values: `arrayIndexed`
	
* `$result[$itemIndex]`
	* Description: An item object.
	* Valid values: `stdClass`
	
* `$result[$itemIndex]->id`
	* Description: ID of updated item.
	* Valid values: `integer`


#### `\DDTools\Storage\Storage::items_updateOne($params)`

* Description: Updates a single item. Calls `\DDTools\Storage\Storage::items_update($params)` with `$params->limit` = `1`.
* Modifiers: `public instance`


##### Parameters

* `$params`
	* Description: Parameters.
	* Valid values:
		* `stdClass`
		* `arrayAssociative`
	* **Required**
	
* `$params->data`
	* Description: New item data. Existing item will be extended by this data. The same parameter as `\DDTools\Storage\Storage::items_update($params)`.
	* Valid values:
		* `object`
		* `array`
	* **Required**
	
* `$params->where`
	* Description: SQL `WHERE` clause. The same parameter as `\DDTools\Storage\Storage::items_update($params)`.
	* Valid values:
		* `stdClass`
		* `arrayAssociative`
		* `string`
		* `null` || `''` — first found item will be updated
	* Default value: `''`
	
* `$params->isEnabledAddNotFound`
	* Description: Is it allowed to add the item if it does not exist?
	* Valid values: `boolean`
	* Default value: `false`


##### Returns

* `$result`
	* Description: An updated (or added) item object or `null` if fail.
	* Valid values:
		* `stdClass`
		* `null`
	
* `$result->id`
	* Description: ID of the item.
	* Valid values: `integer`


#### `\DDTools\Storage\Storage::items_delete($params)`

* Description: Deletes items.
* Modifiers: `abstract public instance`


##### Parameters

* `$params`
	* Description: Parameters.
	* Valid values:
		* `stdClass`
		* `arrayAssociative`
	* Default value: —
	
* `$params->where`
	* Description: SQL `WHERE` clause. The same parameter as `\DDTools\Storage\Storage::items_update($params)`.
	* Valid values:
		* `stdClass`
		* `arrayAssociative`
		* `string`
		* `null` || `''` — all items will be deleted
	* Default value: `''`
	
* `$params->limit`
	* Description: Maximum number of items to delete.
	* Valid values:
		* `integer`
		* `0` — all matching items
	* Default value: `0`
	
* `$params->offset`
	* Description: Offset of the first item (can be useful with `$params->limit`).
	* Valid values: `integer`
	* Default value: `0`
	
* `$params->orderBy`
	* Description: SQL `ORDER BY` clause (can be useful with `$params->limit`).
	* Valid values: `string`
	* Default value: `''`


##### Returns

* `$result`
	* Valid values: `void`


#### `\DDTools\Storage\Storage::items_deleteOne($params)`

* Description: Deletes a single item. Calls `\DDTools\Storage\Storage::items_delete($params)` with `$params->limit` = `1`.
* Modifiers: `public instance`


##### Parameters

* `$params`
	* Description: Parameters.
	* Valid values:
		* `stdClass`
		* `arrayAssociative`
	* Default value: —
	
* `$params->where`
	* Description: SQL `WHERE` clause. The same parameter as `\DDTools\Storage\Storage::items_update($params)`.
	* Valid values:
		* `stdClass`
		* `arrayAssociative`
		* `string`
		* `null` || `''` — first found item will be deleted
	* Default value: `''`
	
* `$params->orderBy`
	* Description: SQL `ORDER BY` clause.
	* Valid values: `string`
	* Default value: `''`


#### `\DDTools\Storage\Storage::items_get($params)`

* Description: Gets items.
* Modifiers: `abstract public instance`


##### Parameters

* `$params`
	* Description: Parameters.
	* Valid values:
		* `stdClass`
		* `arrayAssociative`
	* Default value: —
	
* `$params->where`
	* Description: SQL `WHERE` clause. The same parameter as `\DDTools\Storage\Storage::items_update($params)`.
	* Valid values:
		* `stdClass`
		* `arrayAssociative`
		* `string`
		* `null` || `''` — all items will be returned
	* Default value: `''`
	
* `$params->orderBy`
	* Description: SQL `ORDER BY` clause.
	* Valid values: `string`
	* Default value: `''`
	
* `$params->limit`
	* Description: Maximum number of items to return.
	* Valid values:
		* `integer`
		* `0` — all matching items
	* Default value: `0`
	
* `$params->offset`
	* Description: Offset of the first item (can be useful with `$params->limit`).
	* Valid values: `integer`
	* Default value: `0`
	
* `$params->propsToReturn`
	* Description: Required item property names to return.
	* Valid values:
		* `array`
		* `stringCommaSeparated`
		* `'*'` — all properties
	* Default value: `'*'`
	
* `$params->propsToReturn[$i]`
	* Description: A property name.
	* Valid values: `string`
	* **Required**
	* 
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
		* `null` — result array values will be item objects
	* Default value: `null`


##### Returns

* `$result`
	* Description: An array of items. Item property values will be used as result keys if `$params->propAsResultKey` is set.
	* Valid values:
		* `arrayIndexed`
		* `arrayAssociative`
	
* `$result[$itemIndex|$itemFieldValue]`
	* Description: An item object or item property value if specified in `$params->propAsResultValue`.
	* Valid values: `mixed`


#### `\DDTools\Storage\Storage::items_getOne($params)`

* Description: Gets a single item. Calls `\DDTools\Storage\Storage::items_get($params)` with `$params->limit` = `1`.
* Modifiers: `public instance`


##### Parameters

* `$params`
	* Description: Parameters.
	* Valid values:
		* `stdClass`
		* `arrayAssociative`
	* Default value: —
	
* `$params->where`
	* Description: SQL `WHERE` clause. The same parameter as `\DDTools\Storage\Storage::items_update($params)`.
	* Valid values:
		* `stdClass`
		* `arrayAssociative`
		* `string`
		* `null` || `''` — first found item will be returned
	* Default value: `''`
	
* `$params->orderBy`
	* Description: SQL `ORDER BY` clause.
	* Valid values: `string`
	* Default value: `''`
	
* `$params->propsToReturn`
	* Description: Required item property names to return. The same parameter as `\DDTools\Storage\Storage::items_get($params)`.
	* Valid values:
		* `array`
		* `stringCommaSeparated`
		* `'*'` — all properties
	* Default value: `'*'`
	
* `$params->notFoundResult`
	* Description: What will be returned when no items found.
	* Valid values: `mixed`
	* Default value: `null`


##### Returns

* `$result`
	* Description: Found item object or `$params->notFoundResult`.
	* Valid values:
		* `stdClass`
		* `mixed`


#### `\DDTools\Storage\Storage::items_validateData($params)`

* Description: Returns only used properties of `$params->data`. Properties with invalid names will be deleted.
* Modifiers: `abstract protected instance`


##### Parameters

* `$params`
	* Description: Parameters.
	* Valid values:
		* `stdClass`
		* `arrayAssociative`
	* Default value: —
	
* `$params->data`
	* Description: An array/object of item properties (e. g. you can use `$_POST`).
	* Valid values:
		* `stdClass`
		* `arrayAssociative`
	* **Required**
	
* `$params->data->{$fieldName}`
	* Description: Key is an item property name, value is a value.
	* Valid values: `mixed`


##### Returns

* `$result`
	* Description: Data with only valid property names.
	* Valid values: `stdClass`


#### `\DDTools\Storage\Storage::items_prepareWhere($params)`

* Description: Builds a `where` clause in the required internal format from externally passed parameters.
* Modifiers: `abstract protected instance`


##### Parameters

* `$params`
	* Description: Parameters.
	* Valid values:
		* `stdClass`
		* `arrayAssociative`
	* Default value: —
	
* `$params->where`
	* Description: Data for `where`. The same parameter as `\DDTools\Storage\Storage::items_update($params)`.
	* Valid values:
		* `stdClass`
		* `arrayAssociative`
		* `string`
		* `null` || `''` — it is not used at all
	* Default value: `''`


##### Returns

* `$result`
	* Description: `where` clause in the required internal format. Empty string means that `where` is not used.
	* Valid values: `string`


#### `\DDTools\Storage\Storage::escapeItemPropValue($params)`

* Description: Escapes an item property value.
* Modifiers: `abstract protected instance`


##### Parameters

* `$params`
	* Description: Parameters.
	* Valid values:
		* `stdClass`
		* `arrayAssociative`
	* **Required**
	
* `$params->propName`
	* Description: Name of item property.
	* Valid values: `string`
	* **Required**
	
* `$params->propValue`
	* Description: Value of item property.
	* Valid values: `string`
	* **Required**


##### Returns

* `$result`
	* Description: Escaped value.
	* Valid values: `string`


### `\DDTools\Storage\DB\Storage`

Database implementation of `\DDTools\Storage\Storage`.
Maps items to a database table: the table is created if it does not exist, missing columns are added on construct.

The `items_*` methods of `\DDTools\Storage\Storage` are available.
Abstract methods are implemented here, `items_*One` methods are inherited.


#### Properties

* `\DDTools\Storage\DB\Storage::$nameAlias`
	* Description: Short table name (e. g. `'web_users'`).
		* Full table name is built from it via `\ddTools::$modx->getFullTableName`.
		* Must be defined in a child class or passed to `\DDTools\Storage\DB\Storage::__construct($params)`.
	* Valid values: `string`
	* Modifiers: `protected instance`
	
* `\DDTools\Storage\DB\Storage::$nameFull`
	* Description: Full table name. Built automatically from `$nameAlias`.
	* Valid values: `string`
	* Modifiers: `protected instance`
	
* `\DDTools\Storage\DB\Storage::$columns`
	* Description: Table columns.
		* Converted to `\DDTools\ObjectCollection` on construct.
		* The `id` column is defined by default (`INTEGER(10) AUTO_INCREMENT PRIMARY KEY`, `isReadOnly` = `true`).
	* Valid values: `\DDTools\ObjectCollection`
	* Modifiers: `protected instance`
	
* `\DDTools\Storage\DB\Storage::$columns->items[$i]`
	* Description: Column data.
	* Valid values: `stdClass`
	
* `\DDTools\Storage\DB\Storage::$columns->items[$i]->name`
	* Description: Column name.
	* Valid values: `string`
	* **Required**
	
* `\DDTools\Storage\DB\Storage::$columns->items[$i]->attrs`
	* Description: Column attributes.
	* Valid values: `string`
	* Default value: `static::$columnsDefaultParams->attrs`
	
* `\DDTools\Storage\DB\Storage::$columns->items[$i]->isReadOnly`
	* Description: Can the column be modified?
	* Valid values: `boolean`
	* Default value: `false`
	
* `\DDTools\Storage\DB\Storage::$columns->items[$i]->isPublic`
	* Description: Can the column be used quite safely?
	* Valid values: `boolean`
	* Default value: `false`
	
* `\DDTools\Storage\DB\Storage::$columns->items[$i]->isComparedCaseSensitive`
	* Description: Should the column be compared case-sensitive in `where` clauses?
	* Valid values: `boolean`
	* Default value: `false`
	
* `\DDTools\Storage\DB\Storage::$columns->items[$i]->isTagsAllowed`
	* Description: Are HTML and MODX tags allowed?
	* Valid values: `boolean`
	* Default value: `false`
	
* `\DDTools\Storage\DB\Storage::$columnsDefaultParams`
	* Description: Default parameters for all columns.  
		If some items are not defined in child classes, parent values will be used.
	* Valid values: `stdClass`
	* Modifiers: `protected static`


#### `\DDTools\Storage\DB\Storage::__construct($params)`

* Modifiers: `public instance`


##### Parameters

* `$params`
	* Description: Parameters.
	* Valid values:
		* `stdClass`
		* `arrayAssociative`
	* Default value: —
	
* `$params->nameAlias`
	* Description: Short table name (e. g. `'site_content'`).
		* You can define it in a child class or pass to the constructor directly.
	* Valid values: `string`
	* Default value: `''`
	
* `$params->columns`
	* Description: Additional columns (that are not defined in the class).
		* Additional columns are considered as public (`isPublic` = `true`) by default.
	* Valid values: `array`
	* Default value: `[]`
	
* `$params->columns[$i]`
	* Description: Column parameters.
	* Valid values:
		* `stdClass`
		* `arrayAssociative`
		* `string` — it can be set as a simple string name if other parameters should be set by default
	
* `$params->columns[$i]->name`
	* Description: Column name.
	* Valid values: `string`
	* **Required**
	
* `$params->columns[$i]->attrs`
	* Description: Column attributes.
	* Valid values: `string`
	* Default value: `static::$columnsDefaultParams->attrs`
	
* `$params->columns[$i]->isReadOnly`
	* Description: Can the column be modified?
	* Valid values: `boolean`
	* Default value: `static::$columnsDefaultParams->isReadOnly`
	
* `$params->columns[$i]->isPublic`
	* Description: Can the column be used quite safely?
	* Valid values: `boolean`
	* Default value: `true`
	
* `$params->columns[$i]->isComparedCaseSensitive`
	* Description: Should the column be compared case-sensitive in `where` clauses?
	* Valid values: `boolean`
	* Default value: `static::$columnsDefaultParams->isComparedCaseSensitive`
	
* `$params->columns[$i]->isTagsAllowed`
	* Description: Are HTML and MODX tags allowed?
	* Valid values: `boolean`
	* Default value: `static::$columnsDefaultParams->isTagsAllowed`


#### `\DDTools\Storage\DB\Storage::items_add($params)`

* Description: Implements `\DDTools\Storage\Storage::items_add($params)`.
* Modifiers: `public instance`


#### `\DDTools\Storage\DB\Storage::items_update($params)`

* Description: Implements `\DDTools\Storage\Storage::items_update($params)`.
	* Read-only columns (`isReadOnly`) cannot be updated.
	* The result item contains the first column value plus `$params->data` (the first column may not be named `id`).
* Modifiers: `public instance`


#### `\DDTools\Storage\DB\Storage::items_delete($params)`

* Description: Implements `\DDTools\Storage\Storage::items_delete($params)`.
* Modifiers: `public instance`


#### `\DDTools\Storage\DB\Storage::items_get($params)`

* Description: Implements `\DDTools\Storage\Storage::items_get($params)`.
* Modifiers: `public instance`


## Examples


### Own table (child class)

Extend `\DDTools\Storage\DB\Storage` and define `$nameAlias` and `$columns`.

```php
class ProductsStorage extends \DDTools\Storage\DB\Storage {
	protected $nameAlias = 'products';
	
	protected $columns = [
		[
			'name' => 'id',
			'attrs' => 'INTEGER(10) AUTO_INCREMENT PRIMARY KEY',
			'isReadOnly' => true,
		],
		[
			'name' => 'sku',
			'isPublic' => true,
		],
		[
			'name' => 'quantity',
			'attrs' => 'INTEGER(10) NOT NULL DEFAULT 0',
			'isPublic' => true,
		],
	];
}

$storage = new ProductsStorage();
```


#### `\DDTools\Storage\DB\Storage::items_add($params)`

```php
$storage->items_add([
	'items' => [
		[
			'sku' => 'A-1',
			'quantity' => 10,
		],
		[
			'sku' => 'B-2',
			'quantity' => 3,
		],
	],
]);
```


#### `\DDTools\Storage\DB\Storage::items_get($params)`

```php
$storage->items_get([
	'where' => [
		'sku' => [
			'A-1',
			'B-2',
		],
	],
]);
```


#### `\DDTools\Storage\DB\Storage::items_update($params)`


##### Update items by `$params->where`

```php
$storage->items_update([
	'where' => [
		'sku' => 'A-1',
	],
	'data' => [
		'quantity' => 0,
	],
]);
```


##### Update all items (`$params->where` is empty)

```php
$storage->items_update([
	'data' => [
		'quantity' => 0,
	],
]);
```


### Existing table (`site_content`)

Use `\DDTools\Storage\DB\Storage` without a child class.
Pass the table name and the columns you need (as strings if column attributes already exist in the table).
The default `id` column is already defined.

Columns that already exist will not be altered; missing ones will be added.

```php
$docsStorage = new \DDTools\Storage\DB\Storage([
	'nameAlias' => 'site_content',
	'columns' => [
		'pagetitle',
		'alias',
		'content',
		'published',
		'parent',
	],
]);
```


#### `\DDTools\Storage\DB\Storage::items_get($params)`

```php
$docsStorage->items_get([
	'where' => [
		'published' => 1,
	],
	'propsToReturn' => [
		'id',
		'pagetitle',
		'alias',
	],
	'orderBy' => 'pagetitle ASC',
]);
```


#### `\DDTools\Storage\DB\Storage::items_update($params)`

```php
$docsStorage->items_update([
	'where' => [
		'id' => 1,
	],
	'data' => [
		'pagetitle' => 'Home',
	],
]);
```


<link rel="stylesheet" type="text/css" href="https://raw.githack.com/DivanDesign/CSS.ddMarkdown/master/style.min.css" />