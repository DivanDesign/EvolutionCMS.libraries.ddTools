<?php
namespace DDTools\Tools\Cache\Storage\Quick;

class Storage extends \DDTools\Tools\Cache\Storage\Storage {
	private static \stdClass $targetObject;
	
	/**
	 * initStatic
	 * @version 1.0 (2024-08-07)
	 * 
	 * @desc Static “constructor”.
	 * 
	 * @return {void}
	 */
	public static function initStatic(): void {
		if (!isset(static::$targetObject)){
			if (
				!\DDTools\Tools\Objects::isPropExists([
					'object' => $_SESSION,
					'propName' => 'ddCache',
				])
			){
				$_SESSION['ddCache'] = new \stdClass();
			}
			
			static::$targetObject = &$_SESSION['ddCache'];
		}
	}
	
	/**
	 * save
	 * @version 1.1 (2024-08-07)
	 * 
	 * @param $params {stdClass|arrayAssociative} — The parameters object.
	 * @param $params->name {string} — Cache name.
	 * @param $params->data {string|array|stdClass} — Data to save.
	 * @param [$params->isExtendEnabled=false] {boolean} — Should existing data be extended by $params->data or overwritten?
	 * 
	 * @return {void}
	 */
	public static function save($params): void {
		$params = (object) $params;
		
		// Save to quick storage
		static::$targetObject->{$params->name} = static::save_prepareData($params);
	}
	
	/**
	 * get
	 * @version 3.0 (2024-08-14)
	 * 
	 * @param $params {stdClass|arrayAssociative} — The parameters object.
	 * @param [$params->name] {string} — Cache name (required if $params->isAdvancedSearchEnabled == false).
	 * @param $params->isAdvancedSearchEnabled {boolean} — Is $params->resourceId, $params->suffix or $params->prefix equal to '*'?
	 * @param $params->resourceId {string|'*'} — Resource ID related to cache (e. g. document ID). Default: null (cache of all resources will be cleared independent of `$params->prefix`).
	 * @param $params->prefix {string|'*'} — Cache prefix.
	 * @param $params->suffix {string|'*'} — Cache suffix.
	 * 
	 * @return $result {stdClass|null} — `null` means that the cache does not exist.
	 * @return $result->{$cacheName} {string|array|stdClass}
	 */
	public static function get($params): ?\stdClass {
		$params = (object) $params;
		
		$result = new \stdClass();
		
		// Simple get one item if pattern is not used
		if (!$params->isAdvancedSearchEnabled){
			$result_resource = \DDTools\Tools\Objects::getPropValue([
				'object' => static::$targetObject,
				'propName' => $params->name,
			]);
			
			if (!is_null($result_resource)){
				$result->{$params->name} = $result_resource;
			}
		}else{
			// Find needed cache items
			foreach(
				static::$targetObject
				as $cacheName
				=> $cacheValue
			){
				if (
					static::isItemNameMatched([
						'name' => $cacheName,
						'resourceId' => $params->resourceId,
						'prefix' => $params->prefix,
						'suffix' => $params->suffix,
					])
				){
					$result->{$cacheName} = $cacheValue;
				}
			}
		}
		
		return
			!\ddTools::isEmpty($result)
			? $result
			: null
		;
	}
	
	/**
	 * delete
	 * @version 3.0 (2024-08-14)
	 * 
	 * @param Clear cache for specified resource or every resources.
	 * 
	 * @param [$params] {stdClass|arrayAssociative} — The parameters object.
	 * @param $params->name {string} — Cache name.
	 * @param $params->resourceId {string|'*'} — Resource ID related to cache (e. g. document ID). Default: null (cache of all resources will be cleared independent of `$params->prefix`).
	 * @param $params->prefix {string|'*'} — Cache prefix.
	 * @param $params->suffix {string|'*'} — Cache suffix.
	 * @param $params->isAdvancedSearchEnabled {boolean} — Is $params->resourceId, $params->suffix or $params->prefix equal to '*'?
	 * 
	 * @return {void}
	 */
	public static function delete($params = []): void {
		$params = (object) $params;
		
		// Clear all cache
		if (\ddTools::isEmpty($params)){
			static::$targetObject = new \stdClass();
		// Clear cache for specified resources
		}else{
			// Simple clear one item if pattern is not used
			if (!$params->isAdvancedSearchEnabled){
				unset(static::$targetObject->{$params->name});
			}else{
				// Find needed cache items
				foreach(
					static::$targetObject
					as $cacheName
					=> $cacheValue
				){
					if (
						static::isItemNameMatched([
							'name' => $cacheName,
							'resourceId' => $params->resourceId,
							'prefix' => $params->prefix,
							'suffix' => $params->suffix,
						])
					){
						unset(static::$targetObject->{$cacheName});
					}
				}
			}
		}
	}
	
	/**
	 * isItemNameMatched
	 * @version 1.0 (2024-08-12)
	 * 
	 * @param $params {stdClass|arrayAssociative} — The parameters object.
	 * @param $params->name {string} — Cache name.
	 * @param $params->resourceId {string|'*'} — Resource ID related to cache (e. g. document ID). Default: null (cache of all resources will be cleared independent of `$params->prefix`).
	 * @param $params->prefix {string|'*'} — Cache prefix.
	 * @param $params->suffix {string|'*'} — Cache suffix.
	 * 
	 * @return {boolean}
	 */
	private static function isItemNameMatched($params): bool {
		$params = (object) $params;
		
		$cacheNameArray = explode(
			'-',
			$params->name
		);
		
		return
			// resourceId
			(
				$params->resourceId == '*'
				|| $cacheNameArray[1] == $params->resourceId
			)
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
		;
	}
}