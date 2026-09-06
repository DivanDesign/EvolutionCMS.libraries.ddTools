# (MODX)EvolutionCMS.libraries.ddTools

A library with various tools facilitating your work.


## Requires

* PHP >= 7.4
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
]);
```

* If `ddTools` is already exist on your site, `ddInstaller` will check it version and update it if needed.
* If `ddTools` is not exist on your site, `ddInstaller` can't do anything because requires it for itself.


## Reference


### [`\ddTools`](docs/en/ddTools.md)

Static helpers: empty values, URLs, templates, `parseText`, renamed params, document ID/URL.


### [`\DDTools\Tools\Files`](docs/en/Tools/Files.md)

File helpers: create, copy and remove directories, transform images (thumbnails, crop, resize, fill, watermark).


### [`\DDTools\Tools\Objects`](docs/en/Tools/Objects.md)

Helpers for objects and arrays: property access, type conversion, extend, unfold.


### [`\DDTools\Tools\Cache`](docs/en/Tools/Cache.md)

You can cache some data (e. g. a snippet result).

There are 2 levels of caching: stable (file-based) and quick (`$_SESSION`-based). All methods utilize both levels automatically.


### [`\DDTools\ObjectCollection`](docs/en/ObjectCollection.md)

Class representing a collection of some objects or arrays.


### [`\DDTools\Storage`](docs/en/Storage.md)

A uniform API for collections of items (`items_add`, `items_update`, `items_delete`, `items_get`).
Use it for a project table or an existing DB table (e. g. `site_content`) instead of raw SQL queries.


### [`\DDTools\Base`](docs/en/Base.md)

Simple abstract class and trait with some small methods facilitating your work.
It is convenient to inherit your classes from `\DDTools\Base\Base` or use `\DDTools\Base\AncestorTrait`.


### [`\DDTools\Snippet`](docs/en/Snippet.md)

Abstract class for snippets.


## Links

* [Home page](https://code.divandesign.ru/modx/ddtools)
* [Telegram chat](https://t.me/dd_code)
* [Packagist](https://packagist.org/packages/dd/evolutioncms-libraries-ddtools)
* [GitHub](https://github.com/DivanDesign/EvolutionCMS.libraries.ddTools)


<link rel="stylesheet" type="text/css" href="https://raw.githack.com/DivanDesign/CSS.ddMarkdown/master/style.min.css" />
