<?php
namespace DDTools\Tools\Cache\Storage;

abstract class Storage {
	/**
	 * initStatic
	 * @version 1.0 (2024-08-07)
	 * 
	 * @desc Static “constructor”.
	 * 
	 * @return {void}
	 */
	abstract public static function initStatic(): void;
	
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
	abstract public static function save($params): void;
	
	/**
	 * save_prepareData
	 * @version 2.0 (2024-08-15)
	 * 
	 * @param $params {stdClass|arrayAssociative} — The parameters object.
	 * @param $params->name {string} — Cache name.
	 * @param $params->data {string|array|stdClass} — Data to prepare.
	 * @param $params->isExtendEnabled {boolean} — Should existing data be extended by $params->data or overwritten?
	 * 
	 * @return {string|array|stdClass}
	 */
	protected static function save_prepareData($params){
		$params = (object) $params;
		
		return
			$params->isExtendEnabled
			// Extend existing
			? \DDTools\Tools\Objects::extend([
				'objects' => [
					\DDTools\Tools\Objects::getPropValue([
						'object' => static::get([
							'name' => $params->name,
							'advancedSearchData' => (object) [
								'isEnabled' => false,
							],
						]),
						'propName' => $params->name,
					]),
					$params->data,
				],
			])
			// Overwrite existing
			: $params->data
		;
	}
	
	/**
	 * get
	 * @version 4.0 (2024-08-15)
	 * 
	 * @param $params {stdClass|arrayAssociative} — The parameters object.
	 * @param $params->name {string} — Cache name.
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
	abstract public static function get($params): ?\stdClass;
	
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
	abstract public static function delete($params = []): void;
	
	/**
	 * isOneItemNameMatched
	 * @version 2.0 (2024-08-15)
	 * 
	 * @param $params {stdClass|arrayAssociative} — The parameters object.
	 * @param $params->name {string} — Cache name.
	 * @param $params->advancedSearchData {stdClass} — Advanced search data.
	 * @param $params->advancedSearchData->resourceId {string|'*'|array} — Resource ID(s) related to cache (e. g. document ID). Pass multiple IDs via array.
	 * @param $params->advancedSearchData->resourceId[$i] {string} — Resource ID.
	 * @param $params->advancedSearchData->prefix {string|'*'} — Cache prefix.
	 * @param $params->advancedSearchData->suffix {string|'*'} — Cache suffix.
	 * 
	 * @return {boolean}
	 */
	protected static function isOneItemNameMatched($params): bool {
		$params = (object) $params;
		
		$cacheNameArray = explode(
			'-',
			$params->name
		);
		
		return
			// resourceId
			(
				// Any
				$params->advancedSearchData->resourceId == '*'
				// Specified
				|| (
					is_array($params->advancedSearchData->resourceId)
					// Multiple
					? in_array(
						$cacheNameArray[1],
						$params->advancedSearchData->resourceId
					)
					// Single
					: $cacheNameArray[1] == $params->advancedSearchData->resourceId
				)
			)
			// prefix
			&& (
				$params->advancedSearchData->prefix == '*'
				|| $cacheNameArray[0] == $params->advancedSearchData->prefix
			)
			// suffix
			&& (
				$params->advancedSearchData->suffix == '*'
				|| $cacheNameArray[2] == $params->advancedSearchData->suffix
			)
		;
	}
}