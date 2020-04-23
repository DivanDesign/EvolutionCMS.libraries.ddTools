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


#### `\DDTools\ObjectTools::extend($params)`

Merge the contents of two or more objects together into the first object.

* `$params`
	* Desctription: Parameters, the pass-by-name style is used.
	* Valid values:
		* `stdClass`
		* `arrayAssociative`
	* **Required**
	
* `$params->objects`
	* Desctription: Objects to merge.
	* Valid values: `array`
	* **Required**
	
* `$params->objects[0]`
	* Desctription: The object to extend. It will receive the new properties.
	* Valid values:
		* `object`
		* `NULL` — pass `NULL` to create the new StdClass.
	* **Required**
	
* `$params->objects[i]`
	* Desctription: An object containing additional properties to merge in.
	* Valid values: `object`
	* **Required**
	
* `$params->deep`
	* Desctription: If true, the merge becomes recursive (aka. deep copy).
	* Valid values: `boolean`
	* Default value: `true`


### Examples


#### `\DDTools\ObjectTools::extend($params)`

Merge two objects, modifying the first.

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

```
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


## [Home page →](https://code.divandesign.biz/modx/ddtools)


<link rel="stylesheet" type="text/css" href="https://DivanDesign.ru/assets/files/ddMarkdown.css" />