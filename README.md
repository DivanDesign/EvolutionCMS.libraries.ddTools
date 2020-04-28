# (MODX)EvolutionCMS.libraries.ddTools

A library with various tools facilitating your work.


## Requires

* PHP >= 5.4
* [(MODX)EvolutionCMS](https://github.com/evolution-cms/evolution) >= 1.1
* [PHP.libraries.phpThumb](http://phpthumb.sourceforge.net) 1.7.13-201406261000 (included)


## Documentation


### Installation


#### Manual

1. Create a new folder `assets/libs/ddTools/`.
2. Extract the archive to the folder.


#### Using [Composer](https://getcomposer.org/)

Just add `dd/modxevo-library-ddtools` to your `composer.json`.
_ddTools version must be 0.14 or higher to use this method. If you use it, the compatibility with all your snippets, modules, etc. that use ddTools versions under 0.14 will be maintained._


### Parameters description


#### `\ddTools::verifyRenamedParams($params)`

The method checks an array for deprecated parameters and writes warning messages into the MODX event log.
It returns an associative array, in which the correct parameter names are the keys and the parameter values are the values.
You can use the `exctract` function to turn the array into variables of the current symbol table.

* `$params`
	* Desctription: Parameters, the pass-by-name style is used.
	* Valid values:
		* `stdClass`
		* `arrayAssociative`
	* **Required**
	
* `$params->params`
	* Desctription: The associative array of the parameters of a snippet, in which the parameter names are the keys and the parameter values are the values.  
		You can directly pass here the `$params` variable if you call the method inside of a snippet.
	* Valid values:
		* `stdClass`
		* `arrayAssociative`
	* **Required**
	
* `$params->compliance`
	* Desctription: An array (or object) of correspondence between new parameter names and old ones, in which the new names are the keys and the old names are the values.
	* Valid values:
		* `stdClass`
		* `arrayAssociative`
	* **Required**
	
* `$params->compliance->{$newName}`
	* Desctription: The old name(s). Use a string for a single name or an array for multiple.
	* Valid values:
		* `string`
		* `array`
	* **Required**
	
* `$params->compliance->{$newName}[i]`
	* Desctription: One of the old names.
	* Valid values: `string`
	* **Required**
	
* `$params->returnCorrectedOnly`
	* Desctription: Need to return only corrected parameters?
	* Valid values: `boolean`
	* Default value: `true`
	
* `$params->writeToLog`
	* Desctription: Write a warning message about deprecated parameters to the CMS event log.
	* Valid values: `boolean`
	* Default value: `true`


##### Returns

* `$result`
	* Desctription: An array, in which the correct parameter names are the keys and the parameter values are the values.  
		Can contains all parameters or only corrected (see `$params->returnCorrectedOnly`).
	* Valid values: `arrayAssociative`
	
* `$result[$newName]`
	* Desctription: A parameter value, in which the correct parameter name is the key and the parameter value is the value.
	* Valid values: `mixed`


#### `\DDTools\ObjectTools::extend($params)`

Merge the contents of two or more objects together into the first object.

* `$params`
	* Desctription: Parameters, the pass-by-name style is used.
	* Valid values:
		* `stdClass`
		* `arrayAssociative`
	* **Required**
	
* `$params->objects`
	* Desctription: Objects or arrays to merge.
	* Valid values: `array`
	* **Required**
	
* `$params->objects[0]`
	* Desctription: The object or array to extend. It will receive the new properties.
	* Valid values:
		* `object`
		* `array`
		* `NULL` — pass `NULL` to create the new StdClass.
	* **Required**
	
* `$params->objects[i]`
	* Desctription: An object or array containing additional properties to merge in.
	* Valid values:
		* `object`
		* `array`
	* **Required**
	
* `$params->deep`
	* Desctription: If true, the merge becomes recursive (aka. deep copy).
	* Valid values: `boolean`
	* Default value: `true`


### Examples


#### Verify renamed snippet params (`\ddTools::verifyRenamedParams($params)`)

Suppose we have the snippet `ddSendFeedback` with the `getEmail` and `getId` parameters.
Over time, we decided to rename the parameters as `docField` and `docId` respectively (as it happened in version 1.9).
And we want to save backward compatibility, the snippet must work with the old names and send message to the MODX event log.

```php
//Include (MODX)EvolutionCMS.libraries.ddTools
require_once(
	$modx->getConfig('base_path') .
	'assets/libs/ddTools/modx.ddtools.class.php'
);

//Backward compatibility
extract(\ddTools::verifyRenamedParams([
	//We called the method inside of a snippet, so its parameters are contained in the `$params` variable (MODX feature)
	'params' => $params,
	'compliance' => [
		//The new name => The old name
		'docField' => 'getEmail',
		'docId' => 'getId'
	]
]));
```

Below we can use `$docField` and `$docId` and not to worry. The method will check everything and will send a message to the MODX event log.

After some time we decided to rename the parameters again as `email_docField` и `email_docId`. Nothing wrong, the method can works with multiple old names, just pass an array:

```php
extract(\ddTools::verifyRenamedParams([
	//We called the method inside of a snippet, so its parameters are contained in the `$params` variable (MODX feature)
	'params' => $params,
	'compliance' => [
		//The new name => The old names
		'email_docField' => [
			'docField',
			'getEmail'
		],
		'email_docId' => [
			'docId',
			'getId'
		]
	],
	//Also you can prevent writing to the CMS event log if you want
	'writeToLog' => false
]));
```


#### `\DDTools\ObjectTools::extend($params)`


##### Merge two objects, modifying the first

```php
var_export(\DDTools\ObjectTools::extend([
	'objects' => [
		(object) [
			'cat' => 'mew',
			'dog' => (object) [
				'name' => 'Floyd',
				'weight' => 6
			],
			'rabbit' => 42
		],
		(object) [
			'dog' => (object) [
				'weight' => 10
			],
			'bird' => 0
		]
	]
]));
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
var_export(\DDTools\ObjectTools::extend([
	'objects' => [
		[
			'cat' => 'mew',
			'dog' => [
				'name' => 'Floyd',
				'weight' => 6
			],
			'rabbit' => 42
		],
		[
			'dog' => (object) [
				'weight' => 10
			],
			'bird' => 0
		]
	]
]));
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


## [Home page →](https://code.divandesign.biz/modx/ddtools)


<link rel="stylesheet" type="text/css" href="https://DivanDesign.ru/assets/files/ddMarkdown.css" />