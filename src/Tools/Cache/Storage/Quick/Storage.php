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
	 * @version 3.0 (2024-08-15)
	 * 
	 * @param $params {stdClass|arrayAssociative} — The parameters object.
	 * @param $params->items {stdClass|arrayAssociative} — Item's data to save.
	 * @param $params->items->{$name} {string|array|stdClass} — Key is a cache name, value is a data.
	 * @param $params->isExtendEnabled {boolean} — Should existing data be extended by $params->data or overwritten?
	 * 
	 * @return {void}
	 */
	public static function save($params): void {
		$params = (object) $params;
		
		foreach (
			$params->items
			as $itemName
			=> $itemData
		){
			// Save to quick storage
			static::$targetObject->{$itemName} = static::save_prepareData([
				'name' => $itemName,
				'data' => $itemData,
				'isExtendEnabled' => $params->isExtendEnabled,
			]);
		}
	}
	
	/**
	 * get
	 * @version 4.0.2 (2024-08-15)
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
	 * @return $result->{$itemName} {string|array|stdClass}
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
				as $itemName
				=> $itemData
			){
				if (
					static::isOneItemNameMatched([
						'name' => $itemName,
						'advancedSearchData' => $params->advancedSearchData,
					])
				){
					$result->{$itemName} = $itemData;
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
	 * @version 4.0.2 (2024-08-15)
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
					as $itemName
					=> $itemData
				){
					if (
						static::isOneItemNameMatched([
							'name' => $itemName,
							'advancedSearchData' => $params->advancedSearchData,
						])
					){
						unset(static::$targetObject->{$itemName});
					}
				}
			}
		}
	}
}