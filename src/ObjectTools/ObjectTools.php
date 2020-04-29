<?php
namespace DDTools;

class ObjectTools {
	/**
	 * extend
	 * @version 1.1.1 (2020-04-29)
	 * 
	 * @see README.md
	 * 
	 * @return {object|array}
	 */
	public static function extend($params){
		//Defaults
		$params = (object) array_merge(
			[
				'deep' => true
			],
			(array) $params
		);
		
		//The first item is the target
		$result = array_shift($params->objects);
		//Empty or invalid target
		if (
			!is_object($result) &&
			!is_array($result)
		){
			$result = new \stdClass();
		}
		
		$isResultObject = is_object($result);
		$checkFunction =
			$isResultObject ?
			'is_object' :
			'is_array'
		;
		
		foreach (
			$params->objects as
			$additionalProps
		){
			//Invalid objects will not be used
			if ($checkFunction($additionalProps)){
				foreach (
					$additionalProps as
					$additionalPropName =>
					$additionalPropValue
				){
					if (
						//If recursive merging is needed
						$params->deep &&
						//And the value is an object
						$checkFunction($additionalPropValue)
					){
						//Start recursion
						$additionalPropValue = self::extend([
							'objects' => [
								(
									$isResultObject ?
									(
										isset($result->{$additionalPropName}) ?
										$result->{$additionalPropName} :
										new \stdClass()
									) :
									(
										isset($result[$additionalPropName]) ?
										$result[$additionalPropName] :
										[]
									)
								),
								$additionalPropValue
							],
							'deep' => true
						]);
					}
					
					//Save the new value (replace preverious or create the new property)
					if ($isResultObject){
						$result->{$additionalPropName} = $additionalPropValue;
					}else{
						$result[$additionalPropName] = $additionalPropValue;
					}
				}
			}
		}
		
		return $result;
	}
	
	/**
	 * isPropExists
	 * @version 1.0 (2020-04-30)
	 * 
	 * @see README.md
	 * 
	 * @return {mixed}
	 */
	public static function isPropExists($params){
		$params = (object) $params;
		
		return
			is_object($params->object) ?
			//Objects
			property_exists(
				$params->object,
				$params->propName
			) :
			//Arrays
			array_key_exists(
				$params->propName,
				$params->object
			)
		;
	}
	
	/**
	 * getPropValue
	 * @version 1.0.1 (2020-04-30)
	 * 
	 * @see README.md
	 * 
	 * @return {mixed}
	 */
	public static function getPropValue($params){
		$params = (object) $params;
		
		return
			!self::isPropExists($params) ?
			//Non-existing properties
			NULL :
			//Existing properties
			(
				is_object($params->object) ?
				//Objects
				$params->object->{$params->propName} :
				//Arrays
				$params->object[$params->propName]
			)
		;
	}
}