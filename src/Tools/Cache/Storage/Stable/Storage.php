<?php
namespace DDTools\Tools\Cache\Storage\Stable;

class Storage extends \DDTools\Tools\Cache\Storage\Storage {
	private static string $targetDir;
	
	private static string $contentPrefix = '<?php die("Unauthorized access."); ?>';
	private static int $contentPrefixLen = 37;
	
	/**
	 * initStatic
	 * @version 1.0 (2024-08-07)
	 * 
	 * @desc Static “constructor”.
	 * 
	 * @return {void}
	 */
	public static function initStatic(): void {
		if (!isset(static::$targetDir)){
			static::$targetDir =
				// Path to `assets`
				dirname(
					__DIR__,
					7
				)
				. '/cache/ddCache'
			;
			
			if (!is_dir(static::$targetDir)){
				\DDTools\Tools\Files::createDir([
					'path' => static::$targetDir,
				]);
			}
		}
	}
	
	/**
	 * save
	 * @version 1.0.1 (2024-08-07)
	 * 
	 * @param $params {stdClass|arrayAssociative} — The parameters object.
	 * @param $params->name {string} — Cache name.
	 * @param $params->data {string|array|stdClass} — Data to save.
	 * 
	 * @return {void}
	 */
	public static function save($params): void {
		$params = (object) $params;
		
		// Save cache file
		file_put_contents(
			// Cache file path
			static::buildCacheNamePath($params->name),
			// Cache content
			static::save_prepareData($params)
		);
	}
	
	/**
	 * save_prepareData
	 * @version 1.0 (2024-08-07)
	 * 
	 * @param $params {stdClass|arrayAssociative} — The parameters object.
	 * @param $params->data {string|array|stdClass} — Data to prepare.
	 * 
	 * @return {string}
	 */
	protected static function save_prepareData($params): string {
		$params = (object) $params;
		
		// str|obj|arr
		$dataType =
			is_object($params->data)
			? 'obj'
			: (
				is_array($params->data)
				? 'arr'
				// All other types are considered as string (because of this we don't use the gettype function)
				: 'str'
			)
		;
		
		if ($dataType != 'str'){
			$params->data = \DDTools\Tools\Objects::convertType([
				'object' => $params->data,
				'type' => 'stringJsonAuto',
			]);
		}
		
		return
			static::$contentPrefix
			. $dataType
			. $params->data
		;
	}
	
	/**
	 * get
	 * @version 1.0 (2024-08-07)
	 * 
	 * @param $params {stdClass|arrayAssociative} — The parameters object.
	 * @param $params->name {string} — Cache name.
	 * 
	 * @return {null|string|array|stdClass} — `null` means that the cache does not exist.
	 */
	public static function get($params){
		$params = (object) $params;
		$params->name = static::buildCacheNamePath($params->name);
		
		$result = null;
		
		if (is_file($params->name)){
			// Cut PHP-code prefix
			$result = substr(
				file_get_contents($params->name),
				static::$contentPrefixLen
			);
			
			// str|obj|arr
			$dataType = substr(
				$result,
				0,
				3
			);
			
			// Cut dataType
			$result = substr(
				$result,
				3
			);
			
			if ($dataType != 'str'){
				$result = \DDTools\Tools\Objects::convertType([
					'object' => $result,
					'type' =>
						$dataType == 'obj'
						? 'objectStdClass'
						: 'objectArray'
					,
				]);
			}
		}
		
		return $result;
	}
	
	/**
	 * delete
	 * @version 1.0 (2024-08-07)
	 * 
	 * @param Clear cache for specified resource or every resources.
	 * 
	 * @param [$params] {stdClass|arrayAssociative} — The parameters object.
	 * @param $params->name {string} — Cache name.
	 * 
	 * @return {void}
	 */
	public static function delete($params = []): void {
		$params = (object) $params;
		
		// Clear all cache
		if (empty($params->name)){
			\DDTools\Tools\Files::removeDir(static::$targetDir);
		// Clear cache for specified resources
		}else{
			// Clear stable storage
			$files = glob(
				static::buildCacheNamePath($params->name)
			);
			
			foreach (
				$files
				as $filepath
			){
				unlink($filepath);
			}
		}
	}
	
	/**
	 * buildCacheNamePath
	 * @version 1.0 (2024-08-07)
	 * 
	 * @param $cacheName {string} — Cache name.
	 * 
	 * @return {string}
	 */
	private static function buildCacheNamePath($cacheName): string {
		return
			static::$targetDir
			. '/' . $cacheName . '.php'
		;
	}
}