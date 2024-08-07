<?php
namespace DDTools\Tools;

class Cache {
	private static ?string $stableStorage_dir = null;
	private static \stdClass $quickStorage;
	
	private static string $stableStorage_contentPrefix = '<?php die("Unauthorized access."); ?>';
	private static int $stableStorage_contentPrefixLen = 37;
	
	/**
	 * initStatic
	 * @version 2.1.1 (2024-08-07)
	 * 
	 * @desc Static “constructor”.
	 * 
	 * @return {void}
	 */
	private static function initStatic(): void {
		if (is_null(static::$stableStorage_dir)){
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
			
			if (
				!\DDTools\ObjectTools::isPropExists([
					'object' => $_SESSION,
					'propName' => 'ddCache',
				])
			){
				$_SESSION['ddCache'] = new \stdClass();
			}
			
			static::$quickStorage = &$_SESSION['ddCache'];
		}
	}
	
	/**
	 * save
	 * @version 3.1.1 (2024-08-07)
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
		static::$quickStorage->{$cacheNameData->cacheName} = $params->data;
		
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
			$cacheNameData->pathNameFull,
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
	 * @version 3.1.1 (2024-08-07)
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
		if (
			\DDTools\ObjectTools::isPropExists([
				'object' => static::$quickStorage,
				'propName' => $cacheNameData->cacheName,
			])
		){
			$result = static::$quickStorage->{$cacheNameData->cacheName};
		// Then from file
		}elseif (is_file($cacheNameData->pathNameFull)){
			// Cut PHP-code prefix
			$result = substr(
				file_get_contents($cacheNameData->pathNameFull),
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
			static::$quickStorage->{$cacheNameData->cacheName} = $result;
		}
		
		return $result;
	}
	
	/**
	 * delete
	 * @version 2.3.1 (2024-08-07)
	 * 
	 * @param Clear cache files for specified document or every documents.
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
			static::$quickStorage = new \stdClass();
			// Clear stable storage
			\DDTools\Tools\Files::removeDir(static::$stableStorage_dir);
		// Clear cache for specified resources
		}else{
			$cacheNameData = static::buildCacheNameData($params);
			
			// Clear quick storage
			if (
				strpos(
					$cacheNameData->cacheName,
					'*'
				)
				=== false
			){
				unset(static::$quickStorage->{$cacheNameData->cacheName});
			}else{
				foreach(
					static::$quickStorage
					as $cacheName
					=> $cacheValue
				){
					$cacheNameArray = explode(
						'-',
						$cacheName
					);
					
					if (
						// resourceId
						$cacheNameArray[1] == $params->resourceId
						// prefix
						&& (
							$params->prefix == '*'
							|| $cacheNameArray[0] == $params->prefix
						)
						// suffix
						&& (
							$params->suffix == '*'
							|| $cacheNameArray[2] == $params->suffix
						)
					){
						unset(static::$quickStorage->{$cacheName});
					}
				}
			}
			
			// Clear stable storage
			$files = glob(
				$cacheNameData->pathNameFull
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
	 * @version 5.0.2 (2024-08-07)
	 * 
	 * @param $params {stdClass|arrayAssociative} — The parameters object.
	 * @param $params->resourceId {integer} — Document ID related to cache.
	 * @param $params->suffix {string} — Cache suffix. You can use several suffixes with the same `$params->resourceId` to cache some parts within a resource.
	 * @param [$params->prefix='doc'] {string} — Cache prefix.
	 * 
	 * @return $result {stdClass}
	 * @return $result->cacheName {string} — Short cache name, e. g. 'prefix-resourceId-suffix'.
	 * @return $result->pathNameFull {string} — Full file name path.
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
			'cacheName' => $params->prefix . '-' . $params->resourceId . '-' . $params->suffix,
			'pathNameFull' => '',
		];
		
		$result->pathNameFull =
			static::$stableStorage_dir
			. '/' . $result->cacheName . '.php'
		;
		
		return $result;
	}
}