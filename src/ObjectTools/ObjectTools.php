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
}