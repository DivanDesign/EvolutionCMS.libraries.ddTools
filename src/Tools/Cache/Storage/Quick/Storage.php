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
	 * @version 4.0 (2024-08-15)
	 * 
	 * @param $params {stdClass|arrayAssociative} — The parameters object.
	 * @param [$params->name] {string} — Cache name (required if $params->advancedSearchData->isEnabled == false).
	 * @param $params->advancedSearchData {stdClass} — Advanced search data.
	 * @param $params->advancedSearchData->isEnabled {boolean} — Is advanced search enabled?
	 * @param $params->advancedSearchData->resourceId {string|'*'|array} — Resource ID(s) related to cache (e. g. document ID). Pass multiple IDs via array.
	 * @param $params->advancedSearchData->resourceId[$i] {string} — Resource ID.
	 * @param $params->advancedSearchData->prefix {string|'*'} — Cache prefix.
	 * @param $params->advancedSearchData->suffix {string|'*'} — Cache suffix.
	 * 
	 * @return $result {stdClass|null} — `null` means that the cache does not exist.
	 * @return $result->{$cacheName} {string|array|stdClass}
	 */
	public static function get($params): ?\stdClass {
		$params = (object) $params;
		
		$result = new \stdClass();
		
		// Simple get one item if pattern is not used
		if (!$params->advancedSearchData->isEnabled){
			$result_resource = \DDTools\Tools\Objects::getPropValue([
				'object' => static::$targetObject,
				'propName' => $params->name,
			]);
			
			if (!is_null($result_resource)){
				$result->{$params->name} = $result_resource;
			}
		// Advanced search
		}else{
			// Find needed cache items
			foreach(
				static::$targetObject
				as $cacheName
				=> $cacheValue
			){
				if (
					static::isOneItemNameMatched([
						'name' => $cacheName,
						'resourceId' => $params->advancedSearchData->resourceId,
						'prefix' => $params->advancedSearchData->prefix,
						'suffix' => $params->advancedSearchData->suffix,
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
	 * @version 4.0 (2024-08-15)
	 * 
	 * @param Clear cache for specified resource or every resources.
	 * 
	 * @param [$params] {stdClass|arrayAssociative} — The parameters object. If the parameter is omitted or empty, cache of all resources will be cleared.
	 * @param $params->name {string} — Cache name.
	 * @param $params->advancedSearchData {stdClass} — Advanced search data.
	 * @param $params->advancedSearchData->isEnabled {boolean} — Is advanced search enabled?
	 * @param $params->advancedSearchData->resourceId {string|'*'|array} — Resource ID(s) related to cache (e. g. document ID). Pass multiple IDs via array.
	 * @param $params->advancedSearchData->resourceId[$i] {string} — Resource ID.
	 * @param $params->advancedSearchData->prefix {string|'*'} — Cache prefix.
	 * @param $params->advancedSearchData->suffix {string|'*'} — Cache suffix.
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
			if (!$params->advancedSearchData->isEnabled){
				unset(static::$targetObject->{$params->name});
			// Advanced search
			}else{
				// Find needed cache items
				foreach(
					static::$targetObject
					as $cacheName
					=> $cacheValue
				){
					if (
						static::isOneItemNameMatched([
							'name' => $cacheName,
							'resourceId' => $params->advancedSearchData->resourceId,
							'prefix' => $params->advancedSearchData->prefix,
							'suffix' => $params->advancedSearchData->suffix,
						])
					){
						unset(static::$targetObject->{$cacheName});
					}
				}
			}
		}
	}
}