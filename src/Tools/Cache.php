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
	 * @version 3.2.8 (2024-08-19)
	 * 
	 * @param $params {stdClass|arrayAssociative} — The parameters object.
	 * @param $params->data {string|array|stdClass} — Data to save.
	 * @param $params->resourceId {string} — Resource ID related to cache (e. g. document ID).
	 * @param $params->suffix {string} — Cache suffix. You can use several suffixes with the same `$params->resourceId` to cache some parts within a resource.
	 * @param [$params->prefix='doc'] {string} — Cache prefix.
	 * @param [$params->isExtendEnabled=false] {boolean} — Should existing data be extended by $params->data or overwritten?
	 * 
	 * @return {void}
	 */
	public static function save($params): void {
		$params = (object) $params;
		
		static::saveSeveral(
			\DDTools\Tools\Objects::extend([
				'objects' => [
					(object) [
						'items' => [
							$params->resourceId => $params->data,
						],
					],
					$params,
				],
				'extendableProperties' => [
					'resourceId',
					'suffix',
					'prefix',
					'isExtendEnabled',
				],
			])
		);
	}
	
	/**
	 * saveSeveral
	 * @version 1.0 (2024-08-19)
	 * 
	 * @param $params {stdClass|arrayAssociative} — The parameters object.
	 * @param $params->items {stdClass|arrayAssociative} — Items to save.
	 * @param $params->items->{$resourceId} {string|array|stdClass} — Item data to save. Key is resource ID related to cache (e. g. document ID).
	 * @param $params->suffix {string} — Cache suffix.
	 * @param [$params->prefix='doc'] {string} — Cache prefix.
	 * @param [$params->isExtendEnabled=false] {boolean} — Should existing items data be extended by `$params->items` or overwritten?
	 * 
	 * @return {void}
	 */
	public static function saveSeveral($params): void {
		static::initStatic();
		
		$params = \DDTools\Tools\Objects::extend([
			'objects' => [
				(object) [
					'prefix' => 'doc',
					'isExtendEnabled' => false,
				],
				$params,
			],
		]);
		
		$saveParams = (object) [
			'items' => new \stdClass(),
			'isExtendEnabled' => $params->isExtendEnabled,
		];
		
		foreach (
			$params->items
			as $itemName_resourceId
			=> $itemData
		){
			$cacheNameData = static::buildCacheNameData([
				'resourceId' => $itemName_resourceId,
				'prefix' => $params->prefix,
				'suffix' => $params->suffix,
			]);
			
			// We can't save something containing '*' in name
			if (!$cacheNameData->advancedSearchData->isEnabled){
				$saveParams->items->{$cacheNameData->name} = $itemData;
			}
		}
		
		if (!\ddTools::isEmpty($saveParams->items)){
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
	 * @param $params->suffix {string} — Cache suffix.
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
	 * @version 1.1.4 (2024-08-17)
	 * 
	 * @param $params {stdClass|arrayAssociative} — The parameters object.
	 * @param $params->resourceId {string|'*'|array} — Resource ID(s) related to cache (e. g. document ID). Pass multiple IDs via array.
	 * @param $params->resourceId[$i] {string} — Resource ID.
	 * @param $params->suffix {string} — Cache suffix.
	 * @param [$params->prefix='doc'] {string} — Cache prefix.
	 * 
	 * @return $result {stdClass|null} — `null` means that the cache of specified items does not exist.
	 * @return $result->{$resourceName} {string|array|stdClass}
	 */
	public static function getSeveral($params): ?\stdClass {
		static::initStatic();
		
		$params = \DDTools\Tools\Objects::extend([
			'objects' => [
				(object) [
					'prefix' => 'doc',
				],
				$params,
			],
		]);
		
		$cacheNameData = static::buildCacheNameData($params);
		
		// First try to get from quick storage
		$resultCollection = static::$theQuickStorageClass::get($cacheNameData);
		
		$isQuickStorageDataExist = !is_null($resultCollection);
		
		if (!$isQuickStorageDataExist){
			$resultCollection = static::$theStableStorageClass::get($cacheNameData);
		}
		
		// Save absent items to quick storage from stable storage
		if (
			!$isQuickStorageDataExist
			&& !is_null($resultCollection)
		){
			// Save to quick storage
			foreach (
				$resultCollection
				as $itemName
				=> $itemData
			){
				static::$theQuickStorageClass::save([
					'items' => [
						$itemName => $itemData,
					],
					// Nothing to extend
					'isExtendEnabled' => false,
				]);
			}
		}
		
		return $resultCollection;
	}
	
	/**
	 * delete
	 * @version 2.5 (2024-08-14)
	 * 
	 * @param Clear cache for specified resource or every resources.
	 * 
	 * @param [$params] {stdClass|arrayAssociative} — The parameters object.
	 * @param [$params->resourceId=null] {string|'*'|array|null} — Resource ID(s) related to cache (e. g. document ID). Pass multiple IDs via array. If the parameter is null or empty, cache of all resources will be cleared.
	 * @param [$params->resourceId[$i]] {string} — Resource ID.
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
	 * @version 10.0 (2024-08-17)
	 * 
	 * @param $params {stdClass|arrayAssociative} — The parameters object.
	 * @param $params->resourceId {string|'*'|array} — Resource ID(s) related to cache (e. g. document ID). Pass multiple IDs via array.
	 * @param $params->resourceId[$i] {string} — Resource ID.
	 * @param $params->suffix {string|'*'} — Cache suffix. You can use several suffixes with the same `$params->resourceId` to cache some parts within a resource.
	 * @param $params->prefix {string|'*'} — Cache prefix.
	 * 
	 * @return $result {stdClass}
	 * @return $result->name {string} — Cache name, e. g. 'prefix-resourceId-suffix'. If $params->resourceId is array, '*' will be used as resourceId.
	 * @return $result->advancedSearchData {stdClass} — Advanced search data.
	 * @return $result->advancedSearchData->isEnabled {boolean} — Is $params->resourceId, $params->suffix or $params->prefix equal to '*'?
	 * @return $result->advancedSearchData->resourceId {string} — $params->resourceId.
	 * @return $result->advancedSearchData->prefix {string} — $params->prefix.
	 * @return $result->advancedSearchData->suffix {string} — $params->suffix.
	 */
	private static function buildCacheNameData($params): \stdClass {
		$params = (object) $params;
		
		$resourceId =
			is_array($params->resourceId)
			? '*'
			: $params->resourceId
		;
		
		return (object) [
			'name' => $params->prefix . '-' . $resourceId . '-' . $params->suffix,
			'advancedSearchData' => (object) [
				'isEnabled' => (
					$resourceId == '*'
					|| $params->prefix == '*'
					|| $params->suffix == '*'
				),
				'resourceId' => $params->resourceId,
				'prefix' => $params->prefix,
				'suffix' => $params->suffix,
			],
		];
	}
}