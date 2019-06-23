<?php
namespace DDTools;

class ObjectTools
{
	/**
	 * setExistingProps
	 * @version 1.0 (2019-06-22)
	 * 
	 * @desc Sets existing object properties.
	 * 
	 * @param $params {array_associative|stdClass} — The object of params. @required
	 * @param $params->object {object} — The object. @required
	 * @param $params->props {array_associative|stdClass} — The object properties. @required
	 * 
	 * @return {void}
	 */
	public static function setExistingProps($params){
		$params = (object) $params;
		
		$params->props = (object) $params->props;
		
		foreach (
			$params->props as
			$paramName =>
			$paramValue
		){
			if (property_exists(
				$params->object,
				$paramName
			)){
				$params->object->{$paramName} = $paramValue;
			}
		}
	}
	
	/**
	 * createChildInstance
	 * @version 1.0.1 (2019-06-23)
	 * 
	 * @desc Creates an instance of the needed child class (e. g. \ddSendFeedback\Sender\Telegram\Sender).
	 * 
	 * @param $params {array_associative|stdClass} — The object of params. @required
	 * @param $params->parentDir {string} — Directory of the parent file (e. g. __DIR__). @required
	 * @param $params->parentFullClassName {string} — Parent class name including namespace (e. g. __CLASS__). @required
	 * @param $params->childName {string} — Child name. @required
	 * @param $params->childParams {array_associative|stdClass} — Params to be passed to object constructor. Default: [].
	 * @param $params->capitalizeChildName {boolean} — Need to capitalize child name? Default: false.
	 * 
	 * @throws \Exception
	 * 
	 * @return {object}
	 */
	public static function createChildInstance($params){
		//Defaults
		$params = (object) array_merge(
			[
				'childParams' => [],
				'capitalizeChildName' => false
			],
			(array) $params
		);
		
		//Delimiter between parent class name and namespace
		$parentNamespaceDelimiter = strrpos(
			$params->parentFullClassName,
			'\\'
		);
		//Parent classname without namespace
		$parentClassName = substr(
			$params->parentFullClassName,
			$parentNamespaceDelimiter + 1
		);
		//Parent namespace
		$parentNamespace = substr(
			$params->parentFullClassName,
			0,
			$parentNamespaceDelimiter
		);
		
		//Capitalize child name if needed
		if ($params->capitalizeChildName){
			$params->childName = ucfirst(strtolower($params->childName));
		}
		
		$childFullPath =
			$params->parentDir .
			DIRECTORY_SEPARATOR .
			$params->childName .
			DIRECTORY_SEPARATOR .
			$parentClassName . '.php'
		;
		
		//If child exist
		if(is_file($childFullPath)){
			//Iclude child
			require_once($childFullPath);
			
			$childClass =
				$parentNamespace .
				'\\' .
				$params->childName .
				'\\' .
				$parentClassName
			;
			
			return new $childClass($params->childParams);
		}else{
			throw new \Exception(
				$parentClassName . ' “' . $params->childName . '” not found.',
				500
			);
		}
	}
}