# (MODX)EvolutionCMS.libraries.ddTools

A library with various tools facilitating your work.


## Requires

* PHP >= 5.6
* [(MODX)EvolutionCMS](https://github.com/evolution-cms/evolution) >= 1.1
* [PHP.libraries.HJSON](https://github.com/hjson/hjson-php) 2.2 (included)
* [PHP.libraries.phpThumb](http://phpthumb.sourceforge.net) 1.7.19-202210110924 (included)


## Installation


### Manually

1. Create a new folder `assets/libs/ddTools/`.
2. Extract the archive to the folder.


### Using [Composer](https://getcomposer.org/)

Just add `dd/evolutioncms-libraries-ddtools` to your `composer.json`.

_ddTools version must be 0.14 or higher to use this method. If you use it, the compatibility with all your snippets, modules, etc. that use ddTools versions under 0.14 will be maintained._


### Update using [(MODX)EvolutionCMS.libraries.ddInstaller](https://github.com/DivanDesign/EvolutionCMS.libraries.ddInstaller)

Just run the following PHP code in your sources or [Console](https://github.com/vanchelo/MODX-Evolution-Ajax-Console):

```php
//Include (MODX)EvolutionCMS.libraries.ddInstaller
require_once(
	$modx->getConfig('base_path')
	. 'assets/libs/ddInstaller/require.php'
);

//Update (MODX)EvolutionCMS.libraries.ddTools
\DDInstaller::install([
	'url' => 'https://github.com/DivanDesign/EvolutionCMS.libraries.ddTools',
	'type' => 'library',
]);
```

* If `ddTools` is already exist on your site, `ddInstaller` will check it version and update it if needed.
* If `ddTools` is not exist on your site, `ddInstaller` can't do anything because requires it for itself.


## Parameters description


### `\ddTools::isEmpty($value)`

Determines whether a variable is empty.

The following values are considered as empty:
* Empty objects (e. g. `new \stdClass()`).
* Any values equal to `false` (the same as `$value == false`).

* `$value`
	* Description: Value to be checked.
	* Valid values: `mixed`
	* Default value: `null`


### `\ddTools::convertUrlToAbsolute($params)`

Converts relative URLs to absolute.

The method tends to change URL as little as possible and just prepends required scheme and/or host (or sometimes nothing at all).
All kinds of query parameters, hash, ports, etc. are not modified.

* `$params`
	* Description: The parameters object.
	* Valid values:
		* `stdClass`
		* `arrayAssociative`
	* **Required**
	
* `$params->url`
	* Description: Source URL. Can be set as:
		* `'some/url'` — relative
		* `'/some/url'` — relative starting with slash
		* `'example.com/some/url'` — absolute starting with domain
		* `'//example.com/some/url'` — absolute starting with double slash
		* `'https://example.com/some/url'` — absolute starting with scheme
	* Valid values: `string`
	* **Required**
	
* `$params->host`
	* Description: Host for the result URL.
	* Valid values: `string`
	* Default value: `$_SERVER['HTTP_HOST']`
	
* `$params->scheme`
	* Description: Scheme for the result URL.
	* Valid values: `string`
	* Default value: `'https'` or `'http'` depending on `$_SERVER['HTTPS']`


#### Returns

* `$result`
	* Description: Source URL converted to absolute. Always contains scheme.
	* Valid values: `string`


### `\ddTools::getTpl($tpl = '')`

The same as `$modx->getTpl` with some differences:
* This method always returns `string` regardless of the parameter type. For example, `$modx->getTpl(null)` returns `null`, this method returns `''`.
* The parameter is optional. `$modx->getTpl()` throws an error, this method jsut returns `''`.
* `$modx->getTpl('@CODE:')` returns `'@CODE:'`, this method returns `''`.

* `$tpl`
	* Description: Chunk name or inline template.
	* Valid values:
		* `stringChunkName`
		* `string` — use inline templates starting with `@CODE:`
	* Default value: `''`


#### Returns

* `$result`
	* Description: Required template.
	* Valid values: `string`


### `\ddTools::parseText($params)`

Replaces placeholders in a text with required values.

* `$params`
	* Description: The parameters object.
	* Valid values:
		* `stdClass`
		* `arrayAssociative`
	* **Required**
	
* `$params->text`
	* Description: String to parse.
	* Valid values: `string`
	* **Required**
	
* `$params->data`
	* Description:
		The array of additional data has to be replaced in `$params->text`.  
		Nested objects and arrays are supported too:
		* `{"someOne": "1", "someTwo": "test" }` => `[+someOne+], [+someTwo+]`.
		* `{"some": {"a": "one", "b": "two"} }` => `[+some.a+]`, `[+some.b+]`.
		* `{"some": ["one", "two"] }` => `[+some.0+]`, `[+some.1+]`.
	* Valid values:
		* `arrayAssociative`
		* `stdClass`
		* `stringJsonObject` — as [JSON](https://en.wikipedia.org/wiki/JSON)
		* `stringHjsonObject` — as [HJSON](https://hjson.github.io/)
		* `stringQueryFormatted` — as [Query string](https://en.wikipedia.org/wiki/Query_string)
	* Default value: `[]`
	
* `$params->data->{$key}`
	* Description: Key is placeholder name, value is value.
	* Valid values:
		* `string`
		* `boolean` — will be converted to `'1'` or `'0'` respectively
		* `array` — will be unfolded and also will be converted to a JSON string
		* `object` — will be unfolded and also will be converted to a JSON string
	* **Required**
	
* `$params->placeholderPrefix`
	* Description: Placeholders prefix.
	* Valid values: `string`
	* Default value: `'[+'`
	
* `$params->placeholderSuffix`
	* Description: Placeholders suffix.
	* Valid values: `string`
	* Default value: `'+]'`
	
* `$params->removeEmptyPlaceholders`
	* Description: Do you need to remove empty placeholders?
	* Valid values: `boolean`
	* Default value: `false`
	
* `$params->isCompletelyParsingEnabled`
	* Description: Additional parsing of document fields, settings, chunks, snippets, URLs — everything.
	* Valid values: `boolean`
	* Default value: `true`


#### Returns

* `$result`
	* Description: Parsed text.
	* Valid values: `string`


### `\ddTools::verifyRenamedParams($params)`

The method checks an array for deprecated parameters and writes warning messages into the MODX event log.
It returns an associative array, in which the correct parameter names are the keys and the parameter values are the values.
You can use the `exctract` function to turn the array into variables of the current symbol table.

* `$params`
	* Description: The parameters object.
	* Valid values:
		* `stdClass`
		* `arrayAssociative`
	* **Required**
	
* `$params->params`
	* Description: The associative array of the parameters of a snippet, in which the parameter names are the keys and the parameter values are the values.  
		You can directly pass here the `$params` variable if you call the method inside of a snippet.
	* Valid values:
		* `stdClass`
		* `arrayAssociative`
	* **Required**
	
* `$params->compliance`
	* Description: An array (or object) of correspondence between new parameter names and old ones, in which the new names are the keys and the old names are the values.
	* Valid values:
		* `stdClass`
		* `arrayAssociative`
	* **Required**
	
* `$params->compliance->{$newName}`
	* Description: The old name(s). Use a string for a single name or an array for multiple.
	* Valid values:
		* `string`
		* `array`
	* **Required**
	
* `$params->compliance->{$newName}[i]`
	* Description: One of the old names.
	* Valid values: `string`
	* **Required**
	
* `$params->returnCorrectedOnly`
	* Description: Need to return only corrected parameters?
	* Valid values: `boolean`
	* Default value: `true`
	
* `$params->writeToLog`
	* Description: Write a warning message about deprecated parameters to the CMS event log.
	* Valid values: `boolean`
	* Default value: `true`


#### Returns

* `$result`
	* Description: An array or object, in which the correct parameter names are the keys and the parameter values are the values.  
		Can contains all parameters or only corrected (see `$params->returnCorrectedOnly`).
	* Valid values:
		* `arrayAssociative` — if `$params->params` set as an array
		* `stdClass` — if `$params->params` set as an object
	
* `$result[$newName]`
	* Description: A parameter value, in which the correct parameter name is the key and the parameter value is the value.
	* Valid values: `mixed`


### `\DDTools\Tools\Files`


#### `\DDTools\Tools\Files::modifyImage($params)`

Modify your images: create thumbnails, crop, resize, fill background color or add watermark.

* `$params`
	* Description: The parameters object.
	* Valid values:
		* `stdClass`
		* `arrayAssociative`
	* **Required**
	
* `$params->sourceFullPathName`
	* Description: Full file path of source image.  
		You can pass a relative path too (e. g. `assets/images/some.jpg`), the method will automatically add `base_path` if needed.
	* Valid values: `string`
	* **Required**
	
* `$params->outputFullPathName`
	* Description: Full file path of result image.
		* You can pass a relative path too (e. g. `assets/images/some.jpg`), the method will automatically add `base_path` if needed.
		* The original image will be overwritten if this parameter is omitted.
	* Valid values: `string`
	* Default value: == `$params->sourceFullPathName`
	
* `$params->transformMode`
	* Description: Transform mode.
	* Valid values:
		* `'resize'` — resize only, the image will be inscribed into the specified sizes with the same proportions
		* `'crop'` — crop only
		* `'resizeAndCrop'` — resize small side then crop big side to the specified value
		* `'resizeAndFill'` — inscribe image into the specified sizes and fill empty space with the specified background (see `$params->backgroundColor`)
	* Default value: `'resize'`
	
* `$params->width`
	* Description: Result image width.  
		In pair width / height only one is required, omitted size will be calculated according to the image proportions.
	* Valid values: `integer`
	* **Required**
	
* `$params->height`
	* Description: Result image height.  
		In pair width / height only one is required, omitted size will be calculated according to the image proportions.
	* Valid values: `integer`
	* **Required**
	
* `$params->allowEnlargement`
	* Description: Allow image enlargement when resizing.
	* Valid values: `boolean`
	* Default value: `false`
	
* `$params->backgroundColor`
	* Description: Result image background color in HEX (used only for `$params->transformMode` == `'resizeAndFill'`).
	* Valid values: `string`
	* Default value: `FFFFFF`
	
* `$params->allowEnlargement`
	* Description: Allow image enlargement when resizing.
	* Valid values: `boolean`
	* Default value: `false`
	
* `$params->quality`
	* Description: JPEG compression level.
	* Valid values: `integer`
	* Default value: `100`
	
* `$params->watermarkImageFullPathName`
	* Description: Specify if you want to overlay your image with watermark.  
		You can pass a relative path too (e. g. `assets/images/some.jpg`), the method will automatically add `base_path` if needed.
	* Valid values: `string`
	* Default value: —


### `\DDTools\Tools\Objects`


#### `\DDTools\Tools\Objects::isPropExists($params)`

Checks if the object, class or array has a property / element.
This is a “syntactic sugar” for checking an element in one way regardless of the “object” type.

The first reason for creating this method is convenience to not thinking about type of “object” variables.
Second, the different order of parameters in the native PHP functions makes us crazy.

* `$params`
	* Description: The parameters object.
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


#### `\DDTools\Tools\Objects::getPropValue($params)`

Get the value of an object property or an array element in any nesting level in one way regardless of the “object” type.

* `$params`
	* Description: The parameters object.
	* Valid values:
		* `stdClass`
		* `arrayAssociative`
	* **Required**
	
* `$params->object`
	* Description: Source object or array.  
		It can be nested, and elements of all levels can be mix of objects and arrays (see Examples below).
	* Valid values:
		* `stdClass`
		* `array`
	* **Required**
	
* `$params->propName`
	* Description: Object property name or array key.  
		You can also use `'.'` to get nested properties. Several examples (see also full Examples below):
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


##### Returns

* `$result`
	* Description: Value of an object property or an array element.
	* Valid values:
		* `mixed`
		* `$params->notFoundResult` — if property not exists


#### `\DDTools\Tools\Objects::convertType($params)`

Converts an object type.
Arrays, [JSON](https://en.wikipedia.org/wiki/JSON) and [Query string](https://en.wikipedia.org/wiki/Query_string) objects are also supported.

* `$params`
	* Description: The parameters object.
	* Valid values:
		* `stdClass`
		* `arrayAssociative`
	* **Required**
	
* `$params->object`
	* Description: Input object | array | encoded string.
	* Valid values:
		* `stdClass`
		* `array`
		* `stringJsonObject` — [JSON](https://en.wikipedia.org/wiki/JSON) object
		* `stringJsonArray` — [JSON](https://en.wikipedia.org/wiki/JSON) array
		* `stringHjsonObject` — [HJSON](https://hjson.github.io/) object
		* `stringHjsonArray` — [HJSON](https://hjson.github.io/) array
		* `stringQueryFormatted` — [Query string](https://en.wikipedia.org/wiki/Query_string)
	* **Required**
	
* `$params->type`
	* Description: Type of resulting object.  
		Values are case insensitive (the following names are equal: `'stringjsonauto'`, `'stringJsonAuto'`, `'STRINGJSONAUTO'`, etc).
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


##### Returns

* `$result`
	* Description: Result type depends on `$params->type`.
	* Valid values:
		* `stdClass`
		* `array`
		* `stringJsonObject`
		* `stringJsonArray`


#### `\DDTools\Tools\Objects::extend($params)`

Merge the contents of two or more objects or arrays together into the first one.

* `$params`
	* Description: The parameters object.
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
		The following values are considered to be empty:
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


#### `\DDTools\Tools\Objects::unfold($params)`

Converts a multidimensional array/object into an one-dimensional one joining the keys with `$params->keySeparator`.
For example, it can be helpful while using placeholders like `[+size.width+]`.

* `$params`
	* Description: The parameters object.
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


##### Returns

* `$result`
	* Description: Unfolded object/array. Type of results depends on `$params->object`.
	* Valid values:
		* `stdClass`
		* `array`


### `\DDTools\Tools\Cache`

You can cache some data (e. g. a snippet result).

* There are 2 levels of caching: stable (file-based) and quick (`$_SESSION`-based). All methods utilize both levels automatically.
* The name of each cache item is `[+prefix+]-[+resourceId+]-[+suffix+]`.
* Each cache item can contain a `string`, `array` or `stdClass`.
* All cache files are stored in the `assets/cache/ddCache` folder.
* The name of each cache file is `[+cacheName+].php`.
* Quick cache items are stored in `$_SESSION['ddCache']`.


#### `\DDTools\Tools\Cache::save($params)`

Saves custom data to cache storage.

* `$params`
	* Description: The parameters object.
	* Valid values:
		* `stdClass`
		* `arrayAssociative`
	* **Required**
	
* `$params->data`
	* Description: Data to save.
	* Valid values:
		* `string`
		* `array`
		* `stdClass`
	* **Required**
	
* `$params->resourceId`
	* Description: Resource ID related to cache (e. g. document ID).
	* Valid values: `string`
	* **Required**
	
* `$params->suffix`
	* Description: Cache suffix. You can use several suffixes with the same `$params->resourceId` to cache some parts within a resource.
	* Valid values: `string`
	* **Required**
	
* `$params->prefix`
	* Description: Cache prefix. Useful if you want to cache some custom data that is not related to any documents.
	* Valid values: `string`
	* Default value: `'doc'`
	
* `$params->isExtendEnabled`
	* Description: Should existing data be extended by `$params->data` or overwritten?
	* Valid values: `boolean`
	* Default value: `false`


#### `\DDTools\Tools\Cache::saveSeveral($params)`

Saves data of several items to cache storage.

* `$params`
	* Description: The parameters object.
	* Valid values:
		* `stdClass`
		* `arrayAssociative`
	* **Required**
	
* `$params->items`
	* Description: Items to save.
	* Valid values:
		* `stdClass`
		* `arrayAssociative`
	* **Required**
	
* `$params->items->{$resourceId}`
	* Description: Item data to save. Key is resource ID related to cache (e. g. document ID).
	* Valid values:
		* `string`
		* `array`
		* `stdClass`
	* **Required**
	
* `$params->suffix`
	* Description: Cache suffix.
	* Valid values: `string`
	* **Required**
	
* `$params->prefix`
	* Description: Cache prefix.
	* Valid values: `string`
	* Default value: `'doc'`
	
* `$params->isExtendEnabled`
	* Description: Should existing items data be extended by `$params->items` or overwritten?
	* Valid values: `boolean`
	* Default value: `false`


#### `\DDTools\Tools\Cache::get($params)`

Retrieves item data from cache storage.

* `$params`
	* Description: The parameters object.
	* Valid values:
		* `stdClass`
		* `arrayAssociative`
	* **Required**
	
* `$params->resourceId`
	* Description: Resource ID related to cache (e. g. document ID).
	* Valid values: `string`
	* **Required**
	
* `$params->suffix`
	* Description: Cache suffix.
	* Valid values: `string`
	* **Required**
	
* `$params->prefix`
	* Description: Cache prefix.
	* Valid values: `string`
	* Default value: `'doc'`


##### Returns

* `$result`
	* Description: Cached data.
	* Valid values:
		* Type of returned data depends on type of saved data:
			* `string`
			* `array`
			* `stdClass`
		* `null` — means that the cache item does not exist


#### `\DDTools\Tools\Cache::getSeveral($params)`

Retrieves data of several items from cache storage.


* `$params`
	* Description: The parameters object.
	* Valid values:
		* `stdClass`
		* `arrayAssociative`
	* **Required**
	
* `$params->resourceId`
	* Description: Resource ID(s) related to cache (e. g. document ID).
	* Valid values:
		* `string`
		* `'*'` — means any ID
		* `array` — you can specify multiple IDs
	* **Required**
	
* `$params->resourceId[$i]`
	* Description: A resource ID.
	* Valid values: `string`
	* **Required**
	
* `$params->suffix`
	* Description: Cache suffix.
	* Valid values: `string`
	* **Required**
	
* `$params->prefix`
	* Description: Cache prefix.
	* Valid values: `string`
	* Default value: `'doc'`


##### Returns

* `$result`
	* Description: Cached items data.
	* Valid values:
		* `stdClass`
		* `null` — means that the cache of specified items does not exist
	
* `$result->{$resourceName}`
	* Description: Cached item data.
		* A key is an item's cache name (`[+prefix+]-[+resourceId+]-[+suffix+]`), a value is a data.
		* Type of returned data depends on type of saved data.
	* Valid values:
		* `string`
		* `array`
		* `stdClass`


#### `\DDTools\Tools\Cache::delete($params)`

Deletes one or more cache items.

* `$params`
	* Description: The parameters object.
	* Valid values:
		* `stdClass`
		* `arrayAssociative`
	* Default value: —
	
* `$params->resourceId`
	* Description: Resource ID(s) related to cache (e. g. document ID).
	* Valid values:
		* `string`
		* `'*'` — means any ID
		* `array` — you can specify multiple IDs
		* `null` — cache of all resources will be cleared independent of `$params->prefix`
	* Default value: `null`
	
* `$params->resourceId[$i]`
	* Description: A resource ID.
	* Valid values: `string`
	* Default value: —
	
* `$params->prefix`
	* Description: Cache prefix. Useful if you want to cache some custom data that is not related to any documents.
	* Valid values:
		* `string`
		* `'*'` — means any prefix
	* Default value: `'doc'`
	
* `$params->suffix`
	* Description: Cache suffix.
	* Valid values:
		* `string`
		* `'*'` — means any suffix
	* Default value: `'*'`


### `\DDTools\ObjectCollection`

Class representing a collection of some objects or arrays.


#### `\DDTools\ObjectCollection::__construct($params)`

* `$params`
	* Description: The parameters object.
	* Valid values:
		* `stdClass`
		* `arrayAssociative`
	* Default value: —
	
* `$params->items`
	* Description: An array of items.  
		You can avoid this parameter to create an empty collection and set items later.
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
		Values are case insensitive (the following names are equal: `'objectstdclass'`, `'objectStdClass'`, `'OBJECTSTDCLASS'`, etc).
	* Valid values:
		* `'objectStdClass'`
		* `'objectArray'`
		* `null` — do not convert type of items, use them as is
	* Default value: `null`


#### `\DDTools\ObjectCollection::setItems($params)`

Sets new collection items. Existing items will be removed.

Has the same parameters as `\DDTools\ObjectCollection::__construct($params)`.


#### `\DDTools\ObjectCollection::addItems($params)`

Appends items onto the end of collection.

Has the same parameters as `\DDTools\ObjectCollection::__construct($params)`.


#### `\DDTools\ObjectCollection::getItems($params)`

Gets an array of required collection items.

* `$params`
	* Description: The parameters object.
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
		For example, it can be useful if items have an ID property or something like that.
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


##### Returns

* `$result`
	* Description: An array of items.
	* Valid values:
		* `arrayIndexed`
		* `arrayAssociative` — item property values will be used as result keys if `$params->propAsResultKey` is set
	
* `$result[$itemIndex|$itemFieldValue]`
	* Description: An item object or item property value if specified in `$params->propAsResultValue`.  
	* Valid values: `mixed`


#### `\DDTools\ObjectCollection::getOneItem($params)`

Gets required item.

* `$params`
	* Description: The parameters object.
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

Counts all items.


#### `\DDTools\ObjectCollection::convertItemsType($params)`

Converts type of needed items in collection.

* `$params`
	* Description: The parameters object.
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

Undates properties of existing items with new values.

* `$params`
	* Description: The parameters object.
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

Deletes required items from collection.

* `$params`
	* Description: The parameters object.
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

Gets an JSON-array of all collection items.


##### Returns

* `$result`
	* Description: An JSON-array of items.
	* Valid values: `stringJsonArray`


#### `\DDTools\ObjectCollection::setOneItemData` (protected)

Sets data of an item object. All setting of an item data inside the class must be use this method.
It's convenient to override this method in child classes if items are not plain objects.

* `$params`
	* Description: The parameters object.
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


#### `\DDTools\ObjectCollection::getOneItemData` (protected)

Returns data of an item object. All getting of an item data inside the class must use this method.
It's convenient to override this method in child classes if items are not plain objects.

* `$params`
	* Description: The parameters object.
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


### `\DDTools\Base\Base`

Simple abstract class with some small methods facilitating your work.
It is convenient to inherit your classes from this.

You can see an example of how it works in the [(MODX)EvolutionCMS.snippets.ddGetDocumentField](https://code.divandesign.ru/modx/ddgetdocumentfield) code.


#### `\DDTools\Base\Base::getClassName()`

Gets data about a class name.


##### Returns

* `$result`
	* Description: Class name data.
	* Valid values: `stdClass`
	
* `$result->full`
	* Description: Full class name including namespace, e. g.: `'\\ddSendFeedback\\Sender\\Email\\Sender'`.
	* Valid values: `string`
	
* `$result->nameShort`
	* Description: Short class name, e. g.: `'Sender'`.
	* Valid values: `string`
	
* `$result->namespaceFull`
	* Description: Namespace, e. g.: `'\\ddSendFeedback\\Sender\\Email'`.
	* Valid values: `string`
	
* `$result->namespaceShort`
	* Description: Last namespace item, e. g.: `'Email'`.
	* Valid values: `string`
	
* `$result->namespacePrefix`
	* Description: Namespace prefix, e. g.: `'\\ddSendFeedback\\Sender'`.
	* Valid values: `string`


#### `\DDTools\Base\Base::setExistingProps($props)`

Sets existing object properties.

* `$props`
	* Description: The object properties.
		* The method sets all existing properties: public, private or protected — it doesn't matter, exactly what you pass will be set.
		* No problem if If some properties are not exist, the method just skip them without errors.
	* Valid values:
		* `arrayAssociative`
		* `object`
		* It can also be set as an object-like string:
			* `stringJsonObject` — as [JSON](https://en.wikipedia.org/wiki/JSON)
			* `stringHjsonObject` — as [HJSON](https://hjson.github.io/)
			* `stringQueryFormatted` — as [Query string](https://en.wikipedia.org/wiki/Query_string)
	* **Required**
	
* `$props->{$propName}`
	* Description: Key is the property name, value is the property value.
	* Valid values: `mixed`
	* **Required**


#### `\DDTools\Base\Base::toArray()`

Returns all properties of this object as an associative array independent of their visibility.


##### Returns

* `$result`
	* Description: An associative array representation of this object.  
		The method returns all existing properties: public, private and protected.
	* Valid values: `arrayAssociative`
	
* `$result[$propName]`
	* Description: The key is the object field name and the value is the object field value.
	* Valid values: `mixed`


#### `\DDTools\Base\Base::toJSON()`

Returns all properties of this object as an JSON string independent of their visibility.


##### Returns

* `$result`
	* Description: An JSON string representation of this object.  
		The method returns all existing properties: public, private and protected.
	* Valid values:
		* `stringJsonObject`
		* `stringJsonArray` — if `$this->toArray` returns indexed array
	
* `$result->{$propName}`
	* Description: The key is the object field name and the value is the object field value.
	* Valid values: `mixed`


#### `\DDTools\Base\Base::__toString()`

The same as `\DDTools\Base\Base::toJSON()`.


### `\DDTools\Base\AncestorTrait`

Simple trait for ancestors with some small methods facilitating your work.

You can see an example of how it works in the [(MODX)EvolutionCMS.snippets.ddGetDocumentField](https://code.divandesign.ru/modx/ddgetdocumentfield) code.


#### `\DDTools\Base\AncestorTrait::createChildInstance($params)`

* `$params`
	* Description: The parameters object.
	* Valid values:
		* `stdClass`
		* `arrayAssociative`
	* **Required**
	
* `$params->name`
	* Description: Short name of child class.
	* Valid values: `string`
	* **Required**
	
* `$params->params`
	* Description: Params to be passed to object constructor.
	* Valid values:
		* `stdClass`
		* `arrayAssociative`
	* Default value: `[]`
	
* `$params->parentDir`
	* Description: Directory of the parent file (e. g. `__DIR__`).
	* Valid values: `string`
	* Default value: — (dirname of a class that uses this trait)
	
* `$params->capitalizeName`
	* Description: Need to capitalize child name?
	* Valid values: `boolean`
	* Default value: `true`


##### Returns

* `$result`
	* Description: The new object instance.
	* Valid values: `object`


#### `\DDTools\Base\AncestorTrait::getChildClassName($params)`

* `$params`
	* Description: The parameters object.
	* Valid values:
		* `stdClass`
		* `arrayAssociative`
	* **Required**
	
* `$params->name`
	* Description: Short name of child class.
	* Valid values: `string`
	* **Required**
	
* `$params->parentDir`
	* Description: Directory of the parent file (e. g. `__DIR__`).
	* Valid values: `string`
	* Default value: — (dirname of a class that uses this trait)
	
* `$params->capitalizeName`
	* Description: Need to capitalize child name?
	* Valid values: `boolean`
	* Default value: `true`


##### Returns

* `$result`
	* Description: Child class name.
	* Valid values: `string`


### `\DDTools\Snippet`

Abstract class for snippets.


#### Properties

* `\DDTools\Snippet::$name`
	* Description: Snippet name (e. g. `ddGetDocuments`).  
		Will be set from namespace in `\DDTools\Snippet::__construct($params)`.  
		You can use it inside child classes: `$this->name`.
	* Valid values: `string`
	* Visibility: `protected`
	
* `\DDTools\Snippet::$version`
	* Description: Snippet version.  
		You **must** define it in your child class declaration.
	* Valid values: `string`
	* Visibility: `protected`
	
* `\DDTools\Snippet::$paths`
	* Description: Snippet paths.  
		Will be set in `\DDTools\Snippet::__construct($params)`.
	* Valid values: `stdClass`
	* Visibility: `protected`
	
* `\DDTools\Snippet::$paths->snippet`
	* Description: Full path to the snippet folder.
	* Valid values: `string`
	
* `\DDTools\Snippet::$paths->src`
	* Description: Ful path to the `src` folder.
	* Valid values: `string`
	
* `\DDTools\Snippet::$params`
	* Description: Snippet params.  
		Will be set in `\DDTools\Snippet::__construct($params)`.  
		You can define default values of parameters as associative array in this field of your child class (e. g. `protected $params = ['someParameter' => 'valueByDefault'];`);.
	* Valid values: `stdClass`
	* Visibility: `protected`
	
* `\DDTools\Snippet::$params->{$paramName}`
	* Description: Key is parameter name, value is value.
	* Valid values: `mixed`
	
* `\DDTools\Snippet::$paramsTypes`
	* Description: Overwrite in child classes if you want to convert some parameters types.  
		Parameters types will be converted respectively with this field in `\DDTools\Snippet::prepareParams`.
	* Valid values: `arrayAssociative`
	* Visibility: `protected`
	
* `\DDTools\Snippet::$paramsTypes[$paramName]`
	* Description: The parameter type.  
		Values are case insensitive (the following names are equal: `'stringjsonauto'`, `'stringJsonAuto'`, `'STRINGJSONAUTO'`, etc).
	* Valid values:
		* `'integer'`
		* `'boolean'`
		* `'objectAuto'`
		* `'objectStdClass'`
		* `'objectArray'`
		* `'stringJsonAuto'`
		* `'stringJsonObject'`
		* `'stringJsonArray'`
	* Visibility: `protected`
	
* `\DDTools\Snippet::$renamedParamsCompliance`
	* Description: Overwrite in child classes if you want to rename some parameters with backward compatibility (see `$params->compliance` of `\ddTools::verifyRenamedParams`).
	* Valid values: `arrayAssociative`
	* Visibility: `protected`


#### `\DDTools\Snippet::__construct($params)`

* `$params`
	* Description: Snippet parameters, the pass-by-name style is used.
	* Valid values:
		* `stdClass`
		* `arrayAssociative`
		* `stringJsonObject`
		* `stringQueryFormatted`
	* Default value: `[]`
	
* `$params->{$paramName}`
	* Description: Key is parameter name, value is value.
	* Valid values: `mixed`
	* **Required**


#### `\DDTools\Snippet::run()`

Abstract method for main snippet action.

You **must** define it in your child class declaration.


#### `\DDTools\Snippet::runSnippet($params)`

Static method for easy running needed snippet using only it's name and parameters (if needed).

* `$params`
	* Description: Snippet parameters, the pass-by-name style is used.
	* Valid values:
		* `stdClass`
		* `arrayAssociative`
		* `stringJsonObject`
		* `stringQueryFormatted`
	* **Required**
	
* `$params->name`
	* Description: The name of the snippet you want to run (e. g. `ddGetDocuments`).
	* Valid values: `string`
	* **Required**
	
* `$params->params`
	* Description: Parameters that will be passed to the snippet constructor.
	* Valid values:
		* `stdClass`
		* `arrayAssociative`
		* `stringJsonObject`
		* `stringQueryFormatted`
	* Default value: —
	
* `$params->params->{$paramName}`
	* Description: Key is parameter name, value is value.
	* Valid values: `mixed`
	* **Required**


## Examples


### `\ddTools::convertUrlToAbsolute($params)`: Convert relative URLs to absolute

`$params->url` can be set in various ways for more convenience:

```php
// Relative
$url = 'some/page?q=42#hash';
// Relative starting with slash
$url = '/some/page?q=42#hash';
// Absolute starting with domain
$url = 'example.com/some/page?q=42#hash';
// Absolute starting with double slash
$url = '//example.com/some/page?q=42#hash';
// Absolute starting with scheme
$url = 'https://example.com/some/page?q=42#hash';
```

```php
\ddTools::convertUrlToAbsolute([
	'url' => $url,
	// The parameter is optional and is used here just for clarity. By default it will be equal to domain of your site.
	'host' => 'example.com',
]);
```

Returns this with any of the above URLs:

```php
'https://example.com/some/page?q=42#hash'
```


### Verify renamed snippet params (`\ddTools::verifyRenamedParams($params)`)

Suppose we have the snippet `ddSendFeedback` with the `getEmail` and `getId` parameters.
Over time, we decided to rename the parameters as `docField` and `docId` respectively (as it happened in version 1.9).
And we want to save backward compatibility, the snippet must work with the old names and send message to the MODX event log.

```php
// Include (MODX)EvolutionCMS.libraries.ddTools
require_once(
	$modx->getConfig('base_path')
	. 'assets/libs/ddTools/modx.ddtools.class.php'
);

// Backward compatibility
extract(\ddTools::verifyRenamedParams([
	// We called the method inside of a snippet, so its parameters are contained in the `$params` variable (MODX feature)
	'params' => $params,
	'compliance' => [
		// The new name => The old name
		'docField' => 'getEmail',
		'docId' => 'getId',
	],
]));
```

Below we can use `$docField` and `$docId` and not to worry. The method will check everything and will send a message to the MODX event log.

After some time we decided to rename the parameters again as `email_docField` и `email_docId`. Nothing wrong, the method can works with multiple old names, just pass an array:

```php
extract(\ddTools::verifyRenamedParams([
	// We called the method inside of a snippet, so its parameters are contained in the `$params` variable (MODX feature)
	'params' => $params,
	'compliance' => [
		// The new name => The old names
		'email_docField' => [
			'docField',
			'getEmail',
		],
		'email_docId' => [
			'docId',
			'getId',
		],
	],
	// Also you can prevent writing to the CMS event log if you want
	'writeToLog' => false,
]));
```


### `\ddTools::parseText($params)`


#### Basic example

```php
\ddTools::parseText([
	'text' => '
		<article>
			<h1>[+title+]</h1>
			[+text+]
			<p>[+authorFirstName+] [+authorLastName+], [+date+].</p>
		</article>
	',
	'data' => [
		'title' => 'Bethink Yourselves!',
		'text' => '<p>Question your loyalty to your country and government and strive for a more just and peaceful society.</p>',
		'authorFirstName' => 'Leo',
		'authorLastName' => 'Tolstoy',
		'date' => '1904',
	],
]);
```

Returns:

```html
<article>
	<h1>Bethink Yourselves!</h1>
	<p>Question your loyalty to your country and government and strive for a more just and peaceful society.</p>
	<p>Leo Tolstoy, 1904.</p>
</article>
```


#### Nested objects in `$params->data`

```php
\ddTools::parseText([
	// Data can have a complex nested structure
	'data' => [
		'title' => 'Bethink Yourselves!',
		'text' => '<p>Question your actions and consider the morality behind them.</p>',
		// Note that this is not a string, but that's okay
		'meta' => [
			// Moreover, any depth is supported
			// And objects are also supported as well as arrays regardless of nesting level
			'author' => (object) [
				'firstName' => 'Leo',
				'lastName' => 'Tolstoy',
			],
			'date' => '1904',
		],
	],
	// For nested data you can use placeholders like '[+meta.date+]' for getting a property
	// Or like '[+meta+]' to get whole object as JSON
	'text' => '
		<article data-meta=\'[+meta+]\'>
			<h1>[+title+]</h1>
			[+text+]
			<p>[+meta.author.firstName+] [+meta.author.lastName+], [+meta.date+].</p>
		</article>
	',
]);
```

Returns:

```html
<article data-meta='{"author":{"firstName":"Leo","lastName":"Tolstoy"},"date":"1904"}'>
	<h1>Bethink Yourselves!</h1>
	<p>Question your actions and consider the morality behind them.</p>
	<p>Leo Tolstoy, 1904.</p>
</article>
```


### `\DDTools\Tools\Objects`


#### `\DDTools\Tools\Objects::convertType($params)`


##### Convert a JSON or Query encoded string to an array

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


##### Convert a Query encoded string to a JSON object string

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


##### Convert a JSON object to a JSON array

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


##### Convert a HJSON encoded string to an object

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


##### Convert an associative array to a string of HTML attributes

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


#### `\DDTools\Tools\Objects::extend($params)`


##### Merge two objects, modifying the first

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


##### Also you can extend arrays

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


##### Moreover, objects can extend arrays and vice versa

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


##### Don't overwrite fields with empty values (`$params->overwriteWithEmpty` == `false`)

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


##### Extending only specific properties from subsequent objects (`$params->extendableProperties`)

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


#### `\DDTools\Tools\Objects::unfold($params)`


##### Unfold an object

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


##### Unfold an array

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


##### Use custom key separator

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


##### Cross-type unfolding (`$params->isCrossTypeEnabled` == `true`)

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

###### Without cross-type unfolding (by default)

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

###### With cross-type unfolding enabled

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


#### `\DDTools\Tools\Objects::isPropExists($params)`

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


#### `\DDTools\Tools\Objects::getPropValue($params)`


##### Get the value of an object property or an array element using the same syntax

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


##### Get the value of an object property or an array element in any nesting level

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


###### Get a first-level property

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


###### Get a second-level property

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


###### Get a third-level property

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


###### Of course, it works fine with single-level objects that contain `'.'` in their property names

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


### `\DDTools\ObjectCollection`


#### Create a collection with items

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


#### Set items as a JSON string

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


#### `\DDTools\ObjectCollection::getItems($params)`


##### Get an array of items using filter (`$params->filter`)


###### Filter by existence of a property

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


###### Filter by a property value

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


###### Filter using several conditions

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


##### Get an associative array of items using a property value as a result key

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


##### Get a one-dimensional array of item property values

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


#### `\DDTools\ObjectCollection::getOneItem($params)`

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


##### Custom results when no items found


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


#### `\DDTools\ObjectCollection::convertItemsType($params)`

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


#### `\DDTools\ObjectCollection::updateItems($params)`

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


#### `\DDTools\ObjectCollection::deleteItems($params)`

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


## Links

* [Home page](https://code.divandesign.ru/modx/ddtools)
* [Telegram chat](https://t.me/dd_code)
* [Packagist](https://packagist.org/packages/dd/evolutioncms-libraries-ddtools)
* [GitHub](https://github.com/DivanDesign/EvolutionCMS.libraries.ddTools)


<link rel="stylesheet" type="text/css" href="https://raw.githack.com/DivanDesign/CSS.ddMarkdown/master/style.min.css" />