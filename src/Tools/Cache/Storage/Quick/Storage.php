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
	 * @version 1.0 (2024-08-07)
	 * 
	 * @param $params {stdClass|arrayAssociative} — The parameters object.
	 * @param $params->name {string} — Cache name.
	 * 
	 * @return {null|string|array|stdClass} — `null` means that the cache does not exist.
	 */
	public static function get($params){
		$params = (object) $params;
		
		return \DDTools\Tools\Objects::getPropValue([
			'object' => static::$targetObject,
			'propName' => $params->name,
		]);
	}
	
	/**
	 * delete
	 * @version 1.0 (2024-08-07)
	 * 
	 * @param Clear cache for specified resource or every resources.
	 * 
	 * @param [$params] {stdClass|arrayAssociative} — The parameters object.
	 * @param $params->name {string} — Cache name.
	 * @param $params->resourceId {integer|null} — Resource ID related to cache (e. g. document ID). Default: null (cache of all resources will be cleared independent of `$params->prefix`).
	 * @param $params->prefix {string|'*'} — Cache prefix.
	 * @param $params->suffix {string|'*'} — Cache suffix.
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
			// Simple clear one item if masks are not used
			if (
				strpos(
					$params->name,
					'*'
				)
				=== false
			){
				unset(static::$targetObject->{$params->name});
			}else{
				// Find needed cache items
				foreach(
					static::$targetObject
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
						unset(static::$targetObject->{$cacheName});
					}
				}
			}
		}
	}
}