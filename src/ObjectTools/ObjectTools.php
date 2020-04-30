<?php
namespace DDTools;

class ObjectTools {
	/**
	 * extend
	 * @version 1.3 (2020-04-30)
	 * 
	 * @see README.md
	 * 
	 * @return {object|array}
	 */
	public static function extend($params){
		//Defaults
		$params = (object) array_merge(
			[
				'deep' => true,
				'overwriteWithEmpty' => true
			],
			(array) $params
		);
		
		//The first item is the target
		$result = array_shift($params->objects);
		//Empty or invalid target
		if (!self::isObjectOrArray($result)){
			$result = new \stdClass();
		}
		
		$isResultObject = is_object($result);
		
		foreach (
			$params->objects as
			$additionalProps
		){
			//Invalid objects will not be used
			if (self::isObjectOrArray($additionalProps)){
				foreach (
					$additionalProps as
					$additionalPropName =>
					$additionalPropValue
				){
					//Is the original property exists
					$isOriginalPropExists = self::isPropExists([
						'object' => $result,
						'propName' => $additionalPropName
					]);
					//Original property value
					$originalPropValue = self::getPropValue([
						'object' => $result,
						'propName' => $additionalPropName
					]);
					
					//The additional property value will be used by default
					$isAdditionalUsed = true;
					
					if (
						//Overwriting with empty value is disabled
						!$params->overwriteWithEmpty &&
						//And original property exists. Because if not exists we must set it in anyway (an empty value is better than nothing, right?)
						$isOriginalPropExists
					){
						//Check if additional property value is empty
						$isAdditionalUsed =
							(
								//Empty string
								(
									is_string($additionalPropValue) &&
									$additionalPropValue == ''
								) ||
								//NULL
								is_null($additionalPropValue) ||
								//Empty object or array
								(
									self::isObjectOrArray($additionalPropValue) &&
									count((array) $additionalPropValue) == 0
								)
							) ?
							//Additional is empty — don't use it
							false:
							//Additional is not empty — use it
							true
						;
						
						if (
							//Additional property value is empty
							!$isAdditionalUsed &&
							//And original property value is empty too
							(
								//Empty string
								(
									is_string($originalPropValue) &&
									$originalPropValue == ''
								) ||
								//NULL
								is_null($originalPropValue) ||
								//Empty object or array
								(
									self::isObjectOrArray($originalPropValue) &&
									count((array) $originalPropValue) == 0
								)
							) &&
							//But they have different types
							$originalPropValue !== $additionalPropValue
						){
							//Okay, overwrite original in this case
							$isAdditionalUsed = true;
						}
					}
					
					//If additional value must be used
					if ($isAdditionalUsed){
						if (
							//If recursive merging is needed
							$params->deep &&
							//And the value is an object or array
							self::isObjectOrArray($additionalPropValue)
						){
							//Init initial property value to extend
							if (!$isOriginalPropExists){
								$originalPropValue =
									gettype($additionalPropValue) == 'object' ?
									new \stdClass() :
									[]
								;
							}
							
							//Start recursion
							$additionalPropValue = self::extend([
								'objects' => [
									$originalPropValue,
									$additionalPropValue
								],
								'deep' => true,
								'overwriteWithEmpty' => $params->overwriteWithEmpty
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
		}
		
		return $result;
	}
	
	
	/**
	 * isObjectOrArray
	 * @version 0.1 (2020-04-30)
	 * 
	 * @todo Should it get $object directly or as $params->object?
	 * 
	 * @return {boolean}
	 */
	private static function isObjectOrArray($object){
		return
			is_object($object) ||
			is_array($object)
		;
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