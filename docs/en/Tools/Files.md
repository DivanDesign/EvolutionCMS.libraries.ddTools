# `\DDTools\Tools\Files`

File helpers: create, copy and remove directories, transform images (thumbnails, crop, resize, fill, watermark).

See also:
* [README](../../../README.md)


## Reference


### `\DDTools\Tools\Files::createDir($params)`

* Description: Makes a directory using `$modx->config['new_folder_permissions']`.
	* Nested directories will be created too.
	* Does not throw an exception if the folder already exists.
* Modifiers: `public static`


#### Parameters

* `$params`
	* Description: Parameters.
	* Valid values:
		* `stdClass`
		* `arrayAssociative`
	* **Required**
	
* `$params->path`
	* Description: The directory path.
	* Valid values: `string`
	* **Required**


#### Returns

* `$result`
	* Description: Success status.
	* Valid values: `boolean`


### `\DDTools\Tools\Files::copyDir($params)`

* Description: Copies a folder with all contents recursively.
* Modifiers: `public static`


#### Parameters

* `$params`
	* Description: Parameters.
	* Valid values:
		* `stdClass`
		* `arrayAssociative`
	* **Required**
	
* `$params->sourcePath`
	* Description: Path to the directory to copy.
	* Valid values: `string`
	* **Required**
	
* `$params->destinationPath`
	* Description: The destination path.
		* Created if it does not exist.
	* Valid values: `string`
	* **Required**


#### Returns

* `$result`
	* Description: `true` on success, `false` on failure (e. g. source path does not exist).
	* Valid values: `boolean`


### `\DDTools\Tools\Files::removeDir($path)`

* Description: Removes a folder with all contents recursively.
	* Returns `true` if the folder does not already exist.
* Modifiers: `public static`


#### Parameters

* `$path`
	* Description: Path to the directory to remove.
	* Valid values: `string`
	* **Required**


#### Returns

* `$result`
	* Description: Success status, `true` if the folder does not already exist.
	* Valid values: `boolean`


### `\DDTools\Tools\Files::modifyImage($params)`

* Description: Modify your images: create thumbnails, crop, resize, fill background color or add watermark.
* Modifiers: `public static`


#### Parameters

* `$params`
	* Description: Parameters.
	* Valid values:
		* `stdClass`
		* `arrayAssociative`
	* **Required**
	
* `$params->sourceFullPathName`
	* Description: Full file path of source image.
		* You can pass a relative path too (e. g. `assets/images/some.jpg`), the method will automatically add `base_path` if needed.
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
		* In pair width / height only one is required, omitted size will be calculated according to the image proportions.
	* Valid values: `integer`
	* **Required**
	
* `$params->height`
	* Description: Result image height.
		* In pair width / height only one is required, omitted size will be calculated according to the image proportions.
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
		* You can pass a relative path too (e. g. `assets/images/some.jpg`), the method will automatically add `base_path` if needed.
	* Valid values: `string`
	* Default value: —


<link rel="stylesheet" type="text/css" href="https://raw.githack.com/DivanDesign/CSS.ddMarkdown/master/style.min.css" />
