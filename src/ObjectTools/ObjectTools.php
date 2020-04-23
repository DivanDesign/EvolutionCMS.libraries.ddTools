<?php
namespace DDTools;

class ObjectTools {
	/**
	 * extend
	 * @version 1.0 (2020-04-23)
	 * 
	 * @see README.md
	 * 
	 * @return {object}
	 */
	public static function extend($params){
		//Defaults
		$params = (object) array_merge(
			[
				'deep' => true
			],
			(array) $params
		);
		
		//The first object is the target
		$result = array_shift($params->objects);
		//Empty or invalid target
		if (!is_object($result)){
			$result = new \stdClass();
		}
		
		foreach (
			$params->objects as
			$additionalProps
		){
			//Invalid objects will not be used
			if (is_object($additionalProps)){
				foreach (
					$additionalProps as
					$additionalPropName =>
					$additionalPropValue
				){
					if (
						//If recursive merging is needed
						$params->deep &&
						//And the value is an object
						is_object($additionalPropValue)
					){
						//Start recursion
						$result->{$additionalPropName} = self::extend([
							'objects' => [
								$result->{$additionalPropName},
								$additionalPropValue
							],
							'deep' => true
						]);
					}else{
						//Save the new value (replace preverious or create the new property)
						$result->{$additionalPropName} = $additionalPropValue;
					}
				}
			}
		}
		
		return $result;
	}
}