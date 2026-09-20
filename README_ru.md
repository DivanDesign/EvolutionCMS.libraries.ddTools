# (MODX)EvolutionCMS.libraries.ddTools

Библиотека с различными инструментами, облегчающими работу.


## Использует

* PHP >= 7.4
* [(MODX)EvolutionCMS](https://github.com/evolution-cms/evolution) >= 1.1
* [PHP.libraries.HJSON](https://github.com/hjson/hjson-php) 2.2 (в комплекте)
* [PHP.libraries.phpThumb](http://phpthumb.sourceforge.net) 1.7.19-202210110924 (в комплекте)


## Установка


### Вручную

1. Создайте новую папку `assets/libs/ddTools/`.
2. Извлеките содержимое архива в неё.


### Используя [Composer](https://getcomposer.org/)

Просто добавьте `dd/evolutioncms-libraries-ddtools` в ваш `composer.json`.

_Версия ddTools должна быть 0.14 или выше, чтобы использовать этот метод. Если вы его используете, совместимость со всеми сниппетами, модулями и т. д., которые используют ddTools версии ниже 0.14, будет сохранена._


### Обновление с помощью [(MODX)EvolutionCMS.libraries.ddInstaller](https://github.com/DivanDesign/EvolutionCMS.libraries.ddInstaller)

Просто вызовите следующий PHP-код в своих исходинках или модуле [Console](https://github.com/vanchelo/MODX-Evolution-Ajax-Console):

```php
// Подключение (MODX)EvolutionCMS.libraries.ddInstaller
require_once(
	$modx->getConfig('base_path')
	. 'assets/libs/ddInstaller/require.php'
);

// Обновление (MODX)EvolutionCMS.libraries.ddTools
\DDInstaller::install([
	'url' => 'https://github.com/DivanDesign/EvolutionCMS.libraries.ddTools',
]);
```

* Если `ddTools` уже есть на вашем сайте, `ddInstaller` проверит его версию и обновит, если нужно.
* Если `ddTools` отсутствует на вашем сайте, `ddInstaller` ничего не сможет сделать, потому что сам зависит от него.


## Справочник

* [`\ddTools`](docs/en/ddTools.md) — Статические хелперы: пустые значения, URL, шаблоны, `parseText`, переименованные параметры, ID/URL документа.
* `\DDTools\Tools\`:
	* [`Files`](docs/en/Tools/Files.md) — Файловые хелперы: создание, копирование и удаление каталогов, трансформация изображений (превью, обрезка, изменение размера, заливка, водяной знак).
	* [`Objects`](docs/en/Tools/Objects.md) — Хелперы для объектов и массивов: доступ к свойствам, преобразование типов, extend, unfold.
	* [`Cache`](docs/en/Tools/Cache.md) — Можно кэшировать данные (например, результат сниппета).
		* Есть 2 уровня кэширования: постоянное (на основе файлов) и быстрое (на основе `$_SESSION`). Все методы используют оба уровня автоматически.
* [`\DDTools\ObjectCollection`](docs/en/ObjectCollection.md) — Класс, представляющий коллекцию объектов или массивов.
* [`\DDTools\Storage`](docs/en/Storage.md) — Единый API для коллекций элементов (`items_add`, `items_update`, `items_delete`, `items_get`).
	* Используйте его для таблицы проекта или существующей таблицы БД (например, `site_content`) вместо сырых SQL-запросов.
* [`\DDTools\Base`](docs/en/Base.md) — Простой абстрактный класс и трейт с небольшими методами, облегчающими работу.
* [`\DDTools\Snippet`](docs/en/Snippet.md) — Абстрактный класс для сниппетов.


## Ссылки

* [Home page](https://code.divandesign.ru/modx/ddtools)
* [Telegram chat](https://t.me/dd_code)
* [Packagist](https://packagist.org/packages/dd/evolutioncms-libraries-ddtools)
* [GitHub](https://github.com/DivanDesign/EvolutionCMS.libraries.ddTools)


<link rel="stylesheet" type="text/css" href="https://raw.githack.com/DivanDesign/CSS.ddMarkdown/master/style.min.css" />
