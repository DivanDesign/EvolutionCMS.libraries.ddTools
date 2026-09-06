# `\DDTools\Base`

Simple abstract class and trait with some small methods facilitating your work.
It is convenient to inherit your classes from `\DDTools\Base\Base` or use `\DDTools\Base\AncestorTrait`.

See also:
* [README](../../README.md)


## Reference


### `\DDTools\Base\Base`

Simple abstract class with some small methods facilitating your work.
It is convenient to inherit your classes from this.

You can see an example of how it works in the [(MODX)EvolutionCMS.snippets.ddGetDocumentField](https://code.divandesign.ru/modx/ddgetdocumentfield) code.


#### `\DDTools\Base\Base::getClassName($classNameFull = null)`

* Description: Gets data about a class name.
* Modifiers: `public static`


##### Parameters

* `$classNameFull`
	* Description: Full class name including namespace.
	* Valid values:
		* `string`
		* `null` — If not specified, will be used `get_called_class()`
	* Default value: `null`


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
	
* `$result->namespacePrefixRoot`
	* Description: Root namespace prefix, e. g.: `'\\ddSendFeedback'`.
	* Valid values: `string`


#### `\DDTools\Base\Base::setExistingProps($props)`

* Description: Sets existing object properties.
* Modifiers: `public instance`

##### Parameters

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

* Description: Returns all properties of this object as an associative array independent of their visibility.
* Modifiers: `public instance`


##### Returns

* `$result`
	* Description: An associative array representation of this object.
		* The method returns all existing properties: public, private and protected.
	* Valid values: `arrayAssociative`
	
* `$result[$propName]`
	* Description: The key is the object field name and the value is the object field value.
	* Valid values: `mixed`


#### `\DDTools\Base\Base::toJSON()`

* Description: Returns all properties of this object as an JSON string independent of their visibility.
* Modifiers: `public instance`


##### Returns

* `$result`
	* Description: An JSON string representation of this object.
		* The method returns all existing properties: public, private and protected.
	* Valid values:
		* `stringJsonObject`
		* `stringJsonArray` — if `$this->toArray` returns indexed array
	
* `$result->{$propName}`
	* Description: The key is the object field name and the value is the object field value.
	* Valid values: `mixed`


#### `\DDTools\Base\Base::__toString()`

* Description: The same as `\DDTools\Base\Base::toJSON()`.
* Modifiers: `public instance`


### `\DDTools\Base\AncestorTrait`

Simple trait for ancestors with some small methods facilitating your work.

You can see an example of how it works in the [(MODX)EvolutionCMS.snippets.ddGetDocumentField](https://code.divandesign.ru/modx/ddgetdocumentfield) code.


#### `\DDTools\Base\AncestorTrait::createChildInstance($params)`

* Description: Creates a new instance of a child class.
* Modifiers: `final public static`


##### Parameters

* `$params`
	* Description: Parameters.
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

* Modifiers: `public static`


##### Parameters

* `$params`
	* Description: Parameters.
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


<link rel="stylesheet" type="text/css" href="https://raw.githack.com/DivanDesign/CSS.ddMarkdown/master/style.min.css" />
