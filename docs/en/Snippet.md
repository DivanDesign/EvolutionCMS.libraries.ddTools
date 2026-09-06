# `\DDTools\Snippet`

Abstract class for snippets.

See also:
* [README](../../README.md)


## Reference


### Properties

* `\DDTools\Snippet::$name`
	* Description: Snippet name (e. g. `ddGetDocuments`).
		* Will be set from namespace in `\DDTools\Snippet::__construct($params)`.
		* You can use it inside child classes: `$this->name`.
	* Valid values: `string`
	* Modifiers: `protected instance`
	
* `\DDTools\Snippet::$version`
	* Description: Snippet version.
		* You **must** define it in your child class declaration.
	* Valid values: `string`
	* Modifiers: `protected instance`
	
* `\DDTools\Snippet::$paths`
	* Description: Snippet full paths.
		* Will be set in `\DDTools\Snippet::__construct($params)`.
	* Valid values: `stdClass`
	* Modifiers: `protected instance`
	
* `\DDTools\Snippet::$paths->snippet`
	* Description: Full path to the snippet folder.
	* Valid values: `string`
	
* `\DDTools\Snippet::$paths->src`
	* Description: Ful path to the `src` folder.
	* Valid values: `string`
	
* `\DDTools\Snippet::$params`
	* Description: Snippet params.
		* Will be set in `\DDTools\Snippet::__construct($params)`.
		* You can define default values of parameters as associative array in this field of your child class (e. g. `protected $params = ['someParameter' => 'valueByDefault'];`).
	* Valid values: `stdClass`
	* Modifiers: `protected instance`
	
* `\DDTools\Snippet::$params->{$paramName}`
	* Description: Key is parameter name, value is value.
	* Valid values: `mixed`
	
* `\DDTools\Snippet::$paramsTypes`
	* Description: Overwrite in child classes if you want to convert some parameters types.
		* Parameters types will be converted respectively with this field in `\DDTools\Snippet::prepareParams`.
	* Valid values: `arrayAssociative`
	* Modifiers: `protected instance`
	
* `\DDTools\Snippet::$paramsTypes[$paramName]`
	* Description: The parameter type.
		* Values are case insensitive (the following names are equal: `'stringjsonauto'`, `'stringJsonAuto'`, `'STRINGJSONAUTO'`, etc).
	* Valid values:
		* `'integer'`
		* `'float'`
		* `'boolean'`
		* `'objectAuto'`
		* `'objectStdClass'`
		* `'objectArray'`
		* `'stringJsonAuto'`
		* `'stringJsonObject'`
		* `'stringJsonArray'`
	
* `\DDTools\Snippet::$renamedParamsCompliance`
	* Description: Overwrite in child classes if you want to rename some parameters with backward compatibility (see `$params->compliance` of `\ddTools::verifyRenamedParams`).
	* Valid values: `arrayAssociative`
	* Modifiers: `protected instance`


### `\DDTools\Snippet::__construct($params)`


#### Parameters

* `$params`
	* Description: Snippet parameters.
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


### `\DDTools\Snippet::run()`

* Description: Abstract method for main snippet action.
	* You **must** define it in your child class declaration.
* Modifiers: `abstract public instance`


### `\DDTools\Snippet::runSnippet($params)`

* Description: Static method for easy running needed snippet using only it's name and parameters (if needed).
* Modifiers: `public static`

#### Parameters

* `$params`
	* Description: Parameters.
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


<link rel="stylesheet" type="text/css" href="https://raw.githack.com/DivanDesign/CSS.ddMarkdown/master/style.min.css" />
