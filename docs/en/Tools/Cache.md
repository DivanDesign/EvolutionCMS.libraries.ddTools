# `\DDTools\Tools\Cache`

You can cache some data (e. g. a snippet result).

* There are 2 levels of caching: stable (file-based) and quick (`$_SESSION`-based). All methods utilize both levels automatically.
* The name of each cache item is `[+prefix+]-[+resourceId+]-[+suffix+]`.
* Each cache item can contain a `string`, `array` or `stdClass`.
* All cache files are stored in the `assets/cache/ddCache` folder.
* The name of each cache file is `[+cacheName+].php`.
* Quick cache items are stored in `$_SESSION['ddCache']`.

See also:
* [README](../../../README.md)


## Reference


### `\DDTools\Tools\Cache::save($params)`

* Description: Saves custom data to cache storage.
* Modifiers: `public static`


#### Parameters

* `$params`
	* Description: Parameters.
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


### `\DDTools\Tools\Cache::saveSeveral($params)`

* Description: Saves data of several items to cache storage.
* Modifiers: `public static`

#### Parameters

* `$params`
	* Description: Parameters.
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


### `\DDTools\Tools\Cache::get($params)`

* Description: Retrieves item data from cache storage.
* Modifiers: `public static`

#### Parameters

* `$params`
	* Description: Parameters.
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


#### Returns

* `$result`
	* Description: Cached data.
	* Valid values:
		* Type of returned data depends on type of saved data:
			* `string`
			* `array`
			* `stdClass`
		* `null` — means that the cache item does not exist


### `\DDTools\Tools\Cache::getSeveral($params)`

* Description: Retrieves data of several items from cache storage.
* Modifiers: `public static`

#### Parameters

* `$params`
	* Description: Parameters.
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


#### Returns

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


### `\DDTools\Tools\Cache::delete($params)`

* Description: Deletes one or more cache items.
* Modifiers: `public static`

#### Parameters

* `$params`
	* Description: Parameters.
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


<link rel="stylesheet" type="text/css" href="https://raw.githack.com/DivanDesign/CSS.ddMarkdown/master/style.min.css" />
