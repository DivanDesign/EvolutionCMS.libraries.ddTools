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
	 * @version 1.0 (2024-08-07)
	 * 
	 * @param $params {stdClass|arrayAssociative} — The parameters object.
	 * @param $params->name {string} — Cache name.
	 * @param $params->data {string|array|stdClass} — Data to save.
	 * 
	 * @return {void}
	 */
	abstract public static function save($params): void;
	
	/**
	 * get
	 * @version 1.0 (2024-08-07)
	 * 
	 * @param $params {stdClass|arrayAssociative} — The parameters object.
	 * @param $params->name {string} — Cache name.
	 * 
	 * @return {null|string|array|stdClass} — `null` means that the cache does not exist.
	 */
	abstract public static function get($params);
	
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
	abstract public static function delete($params = []): void;
}