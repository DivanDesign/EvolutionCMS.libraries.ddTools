# `\ddTools`

Static helpers: empty values, URLs, templates, `parseText`, renamed params, document ID/URL.

See also:
* [README](../../README.md)


## Reference


### `\ddTools::isEmpty($value)`

* Description: Determines whether a variable is empty.
	* The following values are considered as empty:
		* Empty objects (e. g. `new \stdClass()`).
		* Any values equal to `false` (the same as `$value == false`).
* Modifiers: `public static`


#### Parameters

* `$value`
	* Description: Value to be checked.
	* Valid values: `mixed`
	* Default value: `null`


#### Returns

* `$result`
	* Description: `true` if the value is empty, `false` otherwise.
	* Valid values: `boolean`


### `\ddTools::convertUrlToAbsolute($params)`

* Description: Converts relative URLs to absolute.
	* The method tends to change URL as little as possible and just prepends required scheme and/or host (or sometimes nothing at all).
	* All kinds of query parameters, hash, ports, etc. are not modified.
* Modifiers: `public static`


#### Parameters

* `$params`
	* Description: Parameters.
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

* Description: The same as `$modx->getTpl` with some differences:
	* This method always returns `string` regardless of the parameter type. For example, `$modx->getTpl(null)` returns `null`, this method returns `''`.
	* The parameter is optional. `$modx->getTpl()` throws an error, this method jsut returns `''`.
	* `$modx->getTpl('@CODE:')` returns `'@CODE:'`, this method returns `''`.
* Modifiers: `public static`


#### Parameters

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

* Description: Replaces placeholders in a text with required values.
* Modifiers: `public static`


#### Parameters

* `$params`
	* Description: Parameters.
	* Valid values:
		* `stdClass`
		* `arrayAssociative`
	* **Required**
	
* `$params->text`
	* Description: String to parse.
	* Valid values: `string`
	* **Required**
	
* `$params->data`
	* Description: The array of additional data has to be replaced in `$params->text`.  
		* Nested objects and arrays are supported too:
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

* Description: The method checks an array for deprecated parameters and writes warning messages into the MODX event log.
	* It returns an associative array, in which the correct parameter names are the keys and the parameter values are the values.
	* You can use the `exctract` function to turn the array into variables of the current symbol table.
* Modifiers: `public static`


#### Parameters

* `$params`
	* Description: Parameters.
	* Valid values:
		* `stdClass`
		* `arrayAssociative`
	* **Required**
	
* `$params->params`
	* Description: The associative array of the parameters of a snippet, in which the parameter names are the keys and the parameter values are the values.
		* You can directly pass here the `$params` variable if you call the method inside of a snippet.
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
		* Can contains all parameters or only corrected (see `$params->returnCorrectedOnly`).
	* Valid values:
		* `arrayAssociative` — if `$params->params` set as an array
		* `stdClass` — if `$params->params` set as an object
	
* `$result[$newName]`
	* Description: A parameter value, in which the correct parameter name is the key and the parameter value is the value.
	* Valid values: `mixed`


### `\ddTools::getDocumentIdByUrl($url)`

* Description: Gets ID of a document by its URL.
* Modifiers: `public static`


#### Parameters

* `$url`
	* Description: Document URL or relative path.
	* Valid values:
		* `stringUrl`
			* Relative path: `info/about`
			* Absolute URL: `https://example.com/info/about`
			* IDNA ASCII-compatible domains in absolute URLs are also supported.
	* **Required**


#### Returns

* `$result`
	* Description: Document ID, or `0` if the document was not found.
	* Valid values: `integer`


### `\ddTools::getDocumentUrlById($docId)`

* Description: Gets relative URL path of a document by its ID.
* Modifiers: `public static`


#### Parameters

* `$docId`
	* Description: Document ID.
	* Valid values: `integer`
	* **Required**


#### Returns

* `$result`
	* Description: Relative path without leading or trailing slashes.
	* Valid values:
		* `stringUrlRelative`
		* `''` — empty string for site start or invalid ID (not found in `aliasListing`)


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


### Document URL and ID (`\ddTools::getDocumentIdByUrl`, `\ddTools::getDocumentUrlById`)

Resolve document ID ↔ relative URL path.

Suppose document `8` is available at `info/about` on the site:


#### URL → ID (`\ddTools::getDocumentIdByUrl`)

```php
\ddTools::getDocumentIdByUrl('info/about');
// 8

\ddTools::getDocumentIdByUrl('https://example.com/info/about');
// 8

\ddTools::getDocumentIdByUrl('/');
// site_start document ID (from `$modx->getConfig('site_start')`)

\ddTools::getDocumentIdByUrl('unknown/path');
// 0
```


#### ID → URL path (`\ddTools::getDocumentUrlById`)

```php
\ddTools::getDocumentUrlById(8);
// 'info/about'

\ddTools::getDocumentUrlById($modx->getConfig('site_start'));
// ''

\ddTools::getDocumentUrlById(0);
// ''
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


<link rel="stylesheet" type="text/css" href="https://raw.githack.com/DivanDesign/CSS.ddMarkdown/master/style.min.css" />
