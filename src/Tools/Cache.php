<?php
namespace DDTools\Tools;

class Cache {
	private static $theStableStorageClass = '\DDTools\Tools\Cache\Storage\Stable\Storage';
	private static $theQuickStorageClass = '\DDTools\Tools\Cache\Storage\Quick\Storage';
	
	private static bool $isStaticInited = false;
	
	/**
	 * initStatic
	 * @version 2.1.5 (2024-08-07)
	 * 
	 * @desc Static “constructor”.
	 * 
	 * @return {void}
	 */
	private static function initStatic(): void {
		if (!static::$isStaticInited){
			static::$isStaticInited = true;
			
			static::$theStableStorageClass::initStatic();
			static::$theQuickStorageClass::initStatic();
		}
	}
	
	/**
	 * save
	 * @version 3.2.2 (2024-08-14)
	 * 
	 * @param $params {stdClass|arrayAssociative} — The parameters object.
	 * @param $params->resourceId {string} — Resource ID related to cache (e. g. document ID).
	 * @param $params->suffix {string} — Cache suffix. You can use several suffixes with the same `$params->resourceId` to cache some parts within a resource.
	 * @param $params->data {string|array|stdClass} — Data to save.
	 * @param [$params->prefix='doc'] {string} — Cache prefix.
	 * @param [$params->isExtendEnabled=false] {boolean} — Should existing data be extended by $params->data or overwritten?
	 * 
	 * @return {void}
	 */
	public static function save($params): void {
		static::initStatic();
		
		$params = (object) $params;
		
		$cacheNameData = static::buildCacheNameData($params);
		
		// We can't save something containing '*' in name
		if (!$cacheNameData->isAdvancedSearchEnabled){
			$saveParams = (object) [
				'name' => $cacheNameData->name,
				'data' => $params->data,
			];
			
			if (isset($params->isExtendEnabled)){
				$saveParams->isExtendEnabled = $params->isExtendEnabled;
			}
			
			// Save to quick storage
			static::$theQuickStorageClass::save($saveParams);
			
			// Save to stable storage
			static::$theStableStorageClass::save($saveParams);
		}
	}
	
	/**
	 * get
	 * @version 3.1.10 (2024-08-13)
	 * 
	 * @param $params {stdClass|arrayAssociative} — The parameters object.
	 * @param $params->resourceId {string} — Resource ID related to cache (e. g. document ID).
	 * @param $params->suffix {string} — Cache suffix. You can use several suffixes with the same `$params->resourceId` to cache some parts within a resource.
	 * @param [$params->prefix='doc'] {string} — Cache prefix.
	 * 
	 * @return {null|string|array|stdClass} — `null` means that the cache file does not exist.
	 */
	public static function get($params){
		$resultCollection = static::getSeveral($params);
		
		return
			!is_null($resultCollection)
			? current((array) $resultCollection)
			: null
		;
	}
	
	/**
	 * getSeveral
	 * @version 1.0 (2024-08-13)
	 * 
	 * @param $params {stdClass|arrayAssociative} — The parameters object.
	 * @param $params->resourceId {string} — Resource ID related to cache (e. g. document ID).
	 * @param $params->suffix {string} — Cache suffix. You can use several suffixes with the same `$params->resourceId` to cache some parts within a resource.
	 * @param [$params->prefix='doc'] {string} — Cache prefix.
	 * 
	 * @return $result {stdClass|null} — `null` means that the cache of specified items does not exist.
	 * @return $result->{$resourceName} {string|array|stdClass}
	 */
	public static function getSeveral($params): ?\stdClass {
		static::initStatic();
		
		$cacheNameData = static::buildCacheNameData($params);
		
		// First try to get from quick storage
		$resultCollection = static::$theQuickStorageClass::get($cacheNameData);
		
		$isQuickStorageDataExist = !is_null($resultCollection);
		
		if (!$isQuickStorageDataExist){
			$resultCollection = static::$theStableStorageClass::get($cacheNameData);
		}
		
		if (
			!$isQuickStorageDataExist
			&& !is_null($resultCollection)
		){
			// Save to quick storage
			foreach (
				$resultCollection
				as $cacheName
				=> $cacheValue
			){
				static::$theQuickStorageClass::save([
					'name' => $cacheName,
					'data' => $cacheValue,
				]);
			}
		}
		
		return $resultCollection;
	}
	
	/**
	 * delete
	 * @version 2.4.1 (2024-08-12)
	 * 
	 * @param Clear cache for specified resource or every resources.
	 * 
	 * @param [$params] {stdClass|arrayAssociative} — The parameters object.
	 * @param [$params->resourceId=null] {string|null|'*'} — Resource ID related to cache (e. g. document ID). Default: null (cache of all resources will be cleared independent of `$params->prefix`).
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
		if (
			empty($params->resourceId)
			|| (
				$params->resourceId == '*'
				&& $params->prefix == '*'
				&& $params->suffix == '*'
			)
		){
			// Clear quick storage
			static::$theQuickStorageClass::delete();
			// Clear stable storage
			static::$theStableStorageClass::delete();
		// Clear cache for specified resources
		}else{
			$cacheNameData = static::buildCacheNameData($params);
			
			// Clear quick storage
			static::$theQuickStorageClass::delete($cacheNameData);
			// Clear stable storage
			static::$theStableStorageClass::delete($cacheNameData);
		}
	}
	
	/**
	 * buildCacheNameData
	 * @version 8.0 (2024-08-14)
	 * 
	 * @param $params {stdClass|arrayAssociative} — The parameters object.
	 * @param $params->resourceId {string|'*'} — Resource ID related to cache (e. g. document ID).
	 * @param $params->suffix {string|'*'} — Cache suffix. You can use several suffixes with the same `$params->resourceId` to cache some parts within a resource.
	 * @param [$params->prefix='doc'|'*'] {string} — Cache prefix.
	 * 
	 * @return $result {stdClass}
	 * @return $result->name {string} — Cache name, e. g. 'prefix-resourceId-suffix'.
	 * @return $result->isAdvancedSearchEnabled {boolean} — Is $params->resourceId, $params->suffix or $params->prefix equal to '*'?
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
		
		return (object) [
			'name' => $params->prefix . '-' . $params->resourceId . '-' . $params->suffix,
			'isAdvancedSearchEnabled' => (
				$params->resourceId == '*'
				|| $params->prefix == '*'
				|| $params->suffix == '*'
			),
			'resourceId' => $params->resourceId,
			'prefix' => $params->prefix,
			'suffix' => $params->suffix,
		];
	}
}