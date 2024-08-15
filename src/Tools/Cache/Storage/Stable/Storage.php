<?php
namespace DDTools\Tools\Cache\Storage\Stable;

class Storage extends \DDTools\Tools\Cache\Storage\Storage {
	private static string $targetDir;
	
	private static string $contentPrefix = '<?php die("Unauthorized access."); ?>';
	private static int $contentPrefixLen = 37;
	
	/**
	 * initStatic
	 * @version 1.0 (2024-08-07)
	 * 
	 * @desc Static “constructor”.
	 * 
	 * @return {void}
	 */
	public static function initStatic(): void {
		if (!isset(static::$targetDir)){
			static::$targetDir =
				// Path to `assets`
				dirname(
					__DIR__,
					7
				)
				. '/cache/ddCache'
			;
			
			if (!is_dir(static::$targetDir)){
				\DDTools\Tools\Files::createDir([
					'path' => static::$targetDir,
				]);
			}
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
		
		// Save cache file
		file_put_contents(
			// Cache file path
			static::buildCacheNamePath($params->name),
			// Cache content
			static::save_prepareData($params)
		);
	}
	
	/**
	 * save_prepareData
	 * @version 1.1 (2024-08-07)
	 * 
	 * @param $params {stdClass|arrayAssociative} — The parameters object.
	 * @param $params->name {string} — Cache name.
	 * @param $params->data {string|array|stdClass} — Data to prepare.
	 * @param [$params->isExtendEnabled=false] {boolean} — Should existing data be extended by $params->data or overwritten?
	 * 
	 * @return {string}
	 */
	protected static function save_prepareData($params): string {
		$result = parent::save_prepareData($params);
		
		// str|obj|arr
		$dataType =
			is_object($result)
			? 'obj'
			: (
				is_array($result)
				? 'arr'
				// All other types are considered as string (because of this we don't use the gettype function)
				: 'str'
			)
		;
		
		if ($dataType != 'str'){
			$result = \DDTools\Tools\Objects::convertType([
				'object' => $result,
				'type' => 'stringJsonAuto',
			]);
		}
		
		return
			static::$contentPrefix
			. $dataType
			. $result
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
	 * 
	 * @return $result {stdClass|null}
	 * @return $result->{$cacheName} {null|string|array|stdClass} — `null` means that the cache does not exist.
	 */
	public static function get($params): ?\stdClass {
		$params = (object) $params;
		
		$result = new \stdClass();
		
		$filePath = static::buildCacheNamePath($params->name);
		
		// Simple get one item if pattern is not used
		if (!$params->advancedSearchData->isEnabled){
			$result_resource = static::get_oneItem($filePath);
			
			if (!is_null($result_resource)){
				$result->{$params->name} = $result_resource;
			}
		// Advanced search
		}else{
			$files = glob($filePath);
			
			$isResourceIdSingle = !is_array($params->advancedSearchData->resourceId);
			
			foreach (
				$files
				as $filePath
			){
				$cacheName = basename(
					$filePath,
					'.php'
				);
				
				if (
					// Single
					$isResourceIdSingle
					// Multiple
					|| static::isOneItemNameMatched([
						'name' => $cacheName,
						'resourceId' => $params->advancedSearchData->resourceId,
						'prefix' => $params->advancedSearchData->prefix,
						'suffix' => $params->advancedSearchData->suffix,
					])
				){
					$result->{$cacheName} = static::get_oneItem($filePath);
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
	 * get_oneItem
	 * @version 1.0 (2024-08-12)
	 * 
	 * @param $filePath {string} — Cache file path.
	 * 
	 * @return $result {null|string|array|stdClass} — `null` means that the cache does not exist.
	 */
	private static function get_oneItem($filePath){
		$result = null;
		
		if (is_file($filePath)){
			// Cut PHP-code prefix
			$result = substr(
				file_get_contents($filePath),
				static::$contentPrefixLen
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
		}
		
		return $result;
	}
	
	/**
	 * delete
	 * @version 4.0 (2024-08-15)
	 * 
	 * @param Clear cache for specified resource or every resources.
	 * 
	 * @param [$params] {stdClass|arrayAssociative} — The parameters object.
	 * @param $params->name {string} — Cache name.
	 * @param $params->advancedSearchData {stdClass} — Advanced search data.
	 * @param $params->advancedSearchData->isEnabled {boolean} — Is advanced search enabled?
	 * @param $params->advancedSearchData->resourceId {string|'*'|array} — Resource ID(s) related to cache (e. g. document ID). Pass multiple IDs via array.
	 * @param $params->advancedSearchData->resourceId[$i] {string} — Resource ID.
	 * 
	 * @return {void}
	 */
	public static function delete($params = []): void {
		$params = (object) $params;
		
		// Clear all cache
		if (empty($params->name)){
			\DDTools\Tools\Files::removeDir(static::$targetDir);
		// Clear cache for specified resources
		}else{
			$filePath = static::buildCacheNamePath($params->name);
			
			// Simple clear one item if pattern is not used
			if (!$params->advancedSearchData->isEnabled){
				unlink($filePath);
			}else{
				$files = glob($filePath);
				
				$isResourceIdSingle = !is_array($params->advancedSearchData->resourceId);
				
				foreach (
					$files
					as $filePath
				){
					if (
						// Single
						$isResourceIdSingle
						// Multiple
						|| static::isOneItemNameMatched([
							'name' => basename(
								$filePath,
								'.php'
							),
							'resourceId' => $params->advancedSearchData->resourceId,
							'prefix' => $params->advancedSearchData->prefix,
							'suffix' => $params->advancedSearchData->suffix,
						])
					){
						unlink($filePath);
					}
				}
			}
		}
	}
	
	/**
	 * buildCacheNamePath
	 * @version 1.0 (2024-08-07)
	 * 
	 * @param $cacheName {string} — Cache name.
	 * 
	 * @return {string}
	 */
	private static function buildCacheNamePath($cacheName): string {
		return
			static::$targetDir
			. '/' . $cacheName . '.php'
		;
	}
}