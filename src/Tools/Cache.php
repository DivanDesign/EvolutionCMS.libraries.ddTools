<?php
namespace DDTools\Tools;

class Cache {
	private static string $stableStorage_dir;
	private static $theQuickStorageClass = '\DDTools\Tools\Cache\Storage\Quick\Storage';
	
	private static string $stableStorage_contentPrefix = '<?php die("Unauthorized access."); ?>';
	private static int $stableStorage_contentPrefixLen = 37;
	
	private static bool $isStaticInited = false;
	
	/**
	 * initStatic
	 * @version 2.1.4 (2024-08-07)
	 * 
	 * @desc Static “constructor”.
	 * 
	 * @return {void}
	 */
	private static function initStatic(): void {
		if (!static::$isStaticInited){
			static::$isStaticInited = true;
			
			static::$stableStorage_dir =
				// Path to `assets`
				dirname(
					__DIR__,
					4
				)
				. '/cache/ddCache'
			;
			
			if (!is_dir(static::$stableStorage_dir)){
				\DDTools\Tools\Files::createDir([
					'path' => static::$stableStorage_dir,
				]);
			}
			
			static::$theQuickStorageClass::initStatic();
		}
	}
	
	/**
	 * save
	 * @version 3.1.4 (2024-08-07)
	 * 
	 * @param $params {stdClass|arrayAssociative} — The parameters object.
	 * @param $params->resourceId {integer} — Resource ID related to cache (e. g. document ID).
	 * @param $params->suffix {string} — Cache suffix. You can use several suffixes with the same `$params->resourceId` to cache some parts within a resource.
	 * @param $params->data {string|array|stdClass} — Data to save.
	 * @param [$params->prefix='doc'] {string} — Cache prefix.
	 * 
	 * @return {void}
	 */
	public static function save($params): void {
		static::initStatic();
		
		$params = (object) $params;
		
		$cacheNameData = static::buildCacheNameData($params);
		
		// Save to quick storage
		static::$theQuickStorageClass::save([
			'name' => $cacheNameData->name,
			'data' => $params->data,
		]);
		
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
		
		// Save cache file
		file_put_contents(
			// Cache file path
			$cacheNameData->stableStorage,
			// Cache content
			(
				static::$stableStorage_contentPrefix
				. $dataType
				. $params->data
			)
		);
	}
	
	/**
	 * get
	 * @version 3.1.5 (2024-08-07)
	 * 
	 * @param $params {stdClass|arrayAssociative} — The parameters object.
	 * @param $params->resourceId {integer} — Document ID related to cache.
	 * @param $params->suffix {string} — Cache suffix. You can use several suffixes with the same `$params->resourceId` to cache some parts within a resource.
	 * @param [$params->prefix='doc'] {string} — Cache prefix.
	 * 
	 * @return {null|string|array|stdClass} — `null` means that the cache file does not exist.
	 */
	public static function get($params){
		static::initStatic();
		
		$result = null;
		
		$cacheNameData = static::buildCacheNameData($params);
		
		// First try to get from quick storage
		$result = static::$theQuickStorageClass::get([
			'name' => $cacheNameData->name,
		]);
		
		if (
			is_null($result)
			&& is_file($cacheNameData->stableStorage)
		){
			// Cut PHP-code prefix
			$result = substr(
				file_get_contents($cacheNameData->stableStorage),
				static::$stableStorage_contentPrefixLen
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
			
			// Save to quick storage
			static::$theQuickStorageClass::save([
				'name' => $cacheNameData->name,
				'data' => $result,
			]);
		}
		
		return $result;
	}
	
	/**
	 * delete
	 * @version 2.3.5 (2024-08-07)
	 * 
	 * @param Clear cache for specified resource or every resources.
	 * 
	 * @param [$params] {stdClass|arrayAssociative} — The parameters object.
	 * @param [$params->resourceId=null] {integer|null} — Resource ID related to cache (e. g. document ID). Default: null (cache of all resources will be cleared independent of `$params->prefix`).
	 * @param [$params->prefix='doc'] {string|'*'} — Cache prefix.
	 * @param [$params->suffix='*'] {string|'*'} — Cache suffix.
	 * 
	 * @return {void}
	 */
	public static function delete($params = []): void {
		static::initStatic();
		
		$params = \DDTools\Tools\Objects::extend([
			'objects' => [
				(object) [
					'resourceId' => null,
					'prefix' => 'doc',
					'suffix' => '*',
				],
				$params,
			],
		]);
		
		// Clear all cache
		if (empty($params->resourceId)){
			// Clear quick storage
			static::$theQuickStorageClass::delete();
			// Clear stable storage
			\DDTools\Tools\Files::removeDir(static::$stableStorage_dir);
		// Clear cache for specified resources
		}else{
			$cacheNameData = static::buildCacheNameData($params);
			
			// Clear quick storage
			static::$theQuickStorageClass::delete($cacheNameData);
			
			// Clear stable storage
			$files = glob(
				$cacheNameData->stableStorage
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
	 * buildCacheNameData
	 * @version 6.1.1 (2024-08-07)
	 * 
	 * @param $params {stdClass|arrayAssociative} — The parameters object.
	 * @param $params->resourceId {integer} — Document ID related to cache.
	 * @param $params->suffix {string} — Cache suffix. You can use several suffixes with the same `$params->resourceId` to cache some parts within a resource.
	 * @param [$params->prefix='doc'] {string} — Cache prefix.
	 * 
	 * @return $result {stdClass}
	 * @return $result->name {string} — Cache name, e. g. 'prefix-resourceId-suffix'.
	 * @return $result->stableStorage {string} — Full file name path.
	 * @return $result->resourceId {string} — $params->resourceId.
	 * @return $result->prefix {string} — $params->prefix.
	 * @return $result->suffix {string} — $params->suffix.
	 */
	private static function buildCacheNameData($params): \stdClass {
		$params = \DDTools\Tools\Objects::extend([
			'objects' => [
				(object) [
					'prefix' => 'doc',
				],
				$params,
			],
		]);
		
		$result = (object) [
			'name' => $params->prefix . '-' . $params->resourceId . '-' . $params->suffix,
			
			'stableStorage' => '',
			
			'resourceId' => $params->resourceId,
			'prefix' => $params->prefix,
			'suffix' => $params->suffix,
		];
		
		$result->stableStorage =
			static::$stableStorage_dir
			. '/' . $result->name . '.php'
		;
		
		return $result;
	}
}