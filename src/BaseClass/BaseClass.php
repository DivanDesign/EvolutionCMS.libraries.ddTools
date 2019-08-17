<?php
namespace DDTools;

class BaseClass {
	/**
	 * setExistingProps
	 * @version 1.1 (2019-08-16)
	 * 
	 * @desc Sets existing object properties.
	 * 
	 * @param $params {array_associative|stdClass} — The object properties. @required
	 * 
	 * @return {void}
	 */
	public function setExistingProps($props){
		$props = (object) $props;
		
		foreach (
			$props as
			$propName =>
			$propValue
		){
			if (property_exists(
				$this,
				$propName
			)){
				$this->{$propName} = $propValue;
			}
		}
	}
	
	/**
	 * createChildInstance
	 * @version 1.1 (2019-08-16)
	 * 
	 * @throws \Exception
	 * 
	 * @param $params {array_associative|stdClass} — The object of params. @required
	 * @param $params->parentDir {string} — Directory of the parent file (e. g. __DIR__). @required
	 * @param $params->name {string} — Class name. @required
	 * @param $params->params {array_associative|stdClass} — Params to be passed to object constructor. Default: [].
	 * @param $params->capitalizeName {boolean} — Need to capitalize child name? Default: true.
	 * 
	 * @return {object}
	 */
	public static final function createChildInstance($params){
		//Defaults
		$params = (object) array_merge(
			[
				'params' => [],
				'capitalizeChildName' => true
			],
			(array) $params
		);
		
		//Current classname without namespace
		$thisClassName = substr(
			__CLASS__,
			strrpos(
				__CLASS__,
				'\\'
			) + 1
		);
		
		//Capitalize child name if needed
		if ($params->capitalizeName){
			$params->name = ucfirst(strtolower($params->name));
		}
		
		$filePath =
			$params->name .
			DIRECTORY_SEPARATOR .
			$thisClassName .
			'.php'
		;
		
		if(is_file($params->parentDir . DIRECTORY_SEPARATOR . $filePath)){
			require_once($filePath);
			
			$objectClass =
				__NAMESPACE__ .
				'\\' .
				$params->name .
				'\\' .
				$thisClassName
			;
			
			return new $objectClass($params->params);
		}else{
			throw new \Exception(
				$thisClassName . ' “' . $params->name . '” not found.',
				500
			);
		}
	}
}