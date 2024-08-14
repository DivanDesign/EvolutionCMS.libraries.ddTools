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
	 * @version 1.1 (2024-08-07)
	 * 
	 * @param $params {stdClass|arrayAssociative} — The parameters object.
	 * @param $params->name {string} — Cache name.
	 * @param $params->data {string|array|stdClass} — Data to save.
	 * @param [$params->isExtendEnabled=false] {boolean} — Should existing data be extended by $params->data or overwritten?
	 * 
	 * @return {void}
	 */
	abstract public static function save($params): void;
	
	/**
	 * save_prepareData
	 * @version 1.1.3 (2024-08-14)
	 * 
	 * @param $params {stdClass|arrayAssociative} — The parameters object.
	 * @param $params->name {string} — Cache name.
	 * @param $params->data {string|array|stdClass} — Data to prepare.
	 * @param [$params->isExtendEnabled=false] {boolean} — Should existing data be extended by $params->data or overwritten?
	 * 
	 * @return {string|array|stdClass}
	 */
	protected static function save_prepareData($params){
		$params = \DDTools\Tools\Objects::extend([
			'objects' => [
				(object) [
					'isExtendEnabled' => false,
				],
				$params,
			],
		]);
		
		return
			$params->isExtendEnabled
			// Extend existing
			? \DDTools\Tools\Objects::extend([
				'objects' => [
					\DDTools\Tools\Objects::getPropValue([
						'object' => static::get([
							'name' => $params->name,
							'isAdvancedSearchEnabled' => false,
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
	 * @version 3.0 (2024-08-14)
	 * 
	 * @param $params {stdClass|arrayAssociative} — The parameters object.
	 * @param $params->name {string} — Cache name.
	 * @param $params->resourceId {string} — Resource ID related to cache.
	 * @param $params->isAdvancedSearchEnabled {boolean} — Is $params->resourceId, $params->suffix or $params->prefix equal to '*'?
	 * 
	 * @return $result {stdClass|null} — `null` means that the cache does not exist.
	 * @return $result->{$cacheName} {string|array|stdClass}
	 */
	abstract public static function get($params): ?\stdClass;
	
	/**
	 * delete
	 * @version 3.0 (2024-08-14)
	 * 
	 * @param Clear cache for specified resource or every resources.
	 * 
	 * @param [$params] {stdClass|arrayAssociative} — The parameters object.
	 * @param $params->name {string} — Cache name.
	 * @param $params->resourceId {string|null} — Resource ID related to cache (e. g. document ID). Default: null (cache of all resources will be cleared independent of `$params->prefix`).
	 * @param $params->prefix {string|'*'} — Cache prefix.
	 * @param $params->suffix {string|'*'} — Cache suffix.
	 * @param $params->isAdvancedSearchEnabled {boolean} — Is $params->resourceId, $params->suffix or $params->prefix equal to '*'?
	 * 
	 * @return {void}
	 */
	abstract public static function delete($params = []): void;
}