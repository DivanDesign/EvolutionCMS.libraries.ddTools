<?php
namespace DDTools;

class ObjectTools {
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
	 * @version 1.0.1 (2023-12-03)
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
			(
				is_array($params->object) ?
				//Arrays
				array_key_exists(
					$params->propName,
					$params->object
				) :
				//Always not exist for other types for less fragility
				false
			)
		;
	}
	
	/**
	 * getSingleLevelPropValue
	 * @version 1.0 (2023-03-09)
	 * 
	 * @param $params {stdClass|arrayAssociative} — Parameters, the pass-by-name style is used. @required
	 * @param $params->object {stdClass|array} — Source object or array.. @required
	 * @param $params->propName {string|integer} — Object property name or array key. @required
	 * 
	 * @return {mixed|null}
	 */
	private static function getSingleLevelPropValue($params){
		$params = (object) $params;
		
		return
			!self::isPropExists($params) ?
			//Non-existing properties
			null :
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
	
	/**
	 * getPropValue
	 * @version 1.2 (2023-12-03)
	 * 
	 * @see README.md
	 * 
	 * @return {mixed|null}
	 */
	public static function getPropValue($params){
		$params = \DDTools\ObjectTools::extend([
			'objects' => [
				//Defaults
				(object) [
					'notFoundResult' => null
				],
				$params
			]
		]);
		
		//First try to get value by original propName
		$result = self::getSingleLevelPropValue($params);
		
		if (is_null($result)){
			//Unfolding support (e. g. `parentKey.someKey.0`)
			$propNames = explode(
				'.',
				$params->propName
			);
			
			//If first-level exists
			if (
				\DDTools\ObjectTools::isPropExists([
					'object' => $params->object,
					'propName' => $propNames[0]
				])
			){
				$result = $params->object;
				
				//Find needed value
				foreach (
					$propNames as
					$propName
				){
					//If need to see deeper
					if (self::isObjectOrArray($result)){
						$result = self::getSingleLevelPropValue([
							'object' => $result,
							'propName' => $propName
						]);
					}else{
						//We need to look deeper, but we can't
						$result = null;
						
						break;
					}
				}
			}
		}
		
		if (
			!is_null($params->notFoundResult) &&
			is_null($result)
		){
			$result = $params->notFoundResult;
		}
		
		return $result;
	}
	
	/**
	 * convertType
	 * @version 1.3 (2023-10-01)
	 * 
	 * @see README.md
	 */
	public static function convertType($params){
		//Defaults
		$params = (object) array_merge(
			[
				'type' => 'objectAuto'
			],
			(array) $params
		);
		
		//Case insensitive parameter value
		$params->type = strtolower($params->type);
		
		$result = $params->object;
		
		//If string is passed, we need to parse it first
		if (!self::isObjectOrArray($params->object)){
			if (empty($params->object)){
				$result = new \stdClass();
			}else{
				$isObjectJson =
					//JSON first letter is `{` or `[`
					in_array(
						substr(
							ltrim($params->object),
							0,
							1
						),
						[
							'{',
							'['
						]
					)
				;
				
				if ($isObjectJson){
					$result = json_decode(
						$params->object,
						$params->type == 'objectarray'
					);
					
					if (is_null($result)){
						//Include PHP.libraries.hjson
						require_once(
							'hjson' .
							DIRECTORY_SEPARATOR .
							'HJSONException.php'
						);
						require_once(
							'hjson' .
							DIRECTORY_SEPARATOR .
							'HJSONUtils.php'
						);
						require_once(
							'hjson' .
							DIRECTORY_SEPARATOR .
							'HJSONParser.php'
						);
						
						try {
							$hjsonParser = new \HJSON\HJSONParser();
							
							$result = $hjsonParser->parse(
								$params->object,
								[
									'assoc' => $params->type == 'objectarray'
								]
							);
						}catch (\Exception $e){
							//Flag
							$isObjectJson = false;
						}
					}
				}
				
				//Not JSON
				if (!$isObjectJson){
					//Query string
					parse_str(
						$params->object,
						$result
					);
				}
			}
		}
		
		//stdClass
		if ($params->type == 'objectstdclass'){
			$result = (object) $result;
		//array
		}elseif ($params->type == 'objectarray'){
			$result = (array) $result;
		//stringQueryFormatted
		}elseif (
			$params->type == 'stringqueryformatted' ||
			//Backward compatibility with typo
			$params->type == 'stringqueryformated'
		){
			$result = http_build_query($result);
		//stringHtmlAttrs
		}elseif ($params->type == 'stringhtmlattrs'){
			$resultObject = $result;
			//Temporary use an array
			$result = [];
			
			foreach (
				$resultObject as
				$result_itemAttrName =>
				$result_itemAttrValue
			){
				//Prepare value
				//Boolean to 0|1
				if (is_bool($result_itemAttrValue)){
					$result_itemAttrValue = intval($result_itemAttrValue);
				//Objects to JSON
				}elseif(self::isObjectOrArray($result_itemAttrValue)){
					$result_itemAttrValue = self::convertType([
						'object' => $result_itemAttrValue,
						'type' => 'stringJsonAuto',
					]);
				//Other to string
				}else{
					$result_itemAttrValue = strval($result_itemAttrValue);
				}
				
				$result[] =
					$result_itemAttrName . '=\'' .
					$result_itemAttrValue .
					'\''
				;
			}
			
			$result = implode(
				' ',
				$result
			);
		//stringJson
		}elseif(
			substr(
				$params->type,
				0,
				10
			) == 'stringjson'
		){
			if ($params->type == 'stringjsonobject'){
				$result = (object) $result;
			}elseif ($params->type == 'stringjsonarray'){
				$result = array_values((array) $result);
			}
			
			$result = json_encode(
				$result,
				//JSON_UNESCAPED_UNICODE — Не кодировать многобайтные символы Unicode | JSON_UNESCAPED_SLASHES — Не экранировать /
				JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
			);
		}
		
		return $result;
	}
	
	/**
	 * extend
	 * @version 1.3.10 (2023-03-09)
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
					//Is the source property exists
					$isSourcePropExists = self::isPropExists([
						'object' => $result,
						'propName' => $additionalPropName
					]);
					
					if ($isSourcePropExists){
						//Source property value
						$sourcePropValue = self::getSingleLevelPropValue([
							'object' => $result,
							'propName' => $additionalPropName
						]);
						
						//Is the source property object or array
						$isSourcePropObjectOrArray = self::isObjectOrArray($sourcePropValue);
					}else{
						$sourcePropValue = null;
						$isSourcePropObjectOrArray = false;
					}
					
					//Is the additional property object or array
					$isAdditionalPropObject = is_object($additionalPropValue);
					$isAdditionalPropObjectOrArray =
						$isAdditionalPropObject ||
						is_array($additionalPropValue)
					;
					
					//The additional property value will be used by default
					$isAdditionalPropUsed = true;
					
					if (
						//Overwriting with empty value is disabled
						!$params->overwriteWithEmpty &&
						//And source property exists. Because if not exists we must set it in anyway (an empty value is better than nothing, right?)
						$isSourcePropExists
					){
						//Check if additional property value is empty
						$isAdditionalPropUsed =
							(
								//Empty object or array
								(
									$isAdditionalPropObjectOrArray &&
									count((array) $additionalPropValue) == 0
								) ||
								//Empty string
								(
									is_string($additionalPropValue) &&
									$additionalPropValue == ''
								) ||
								//NULL
								is_null($additionalPropValue)
							) ?
							//Additional is empty — don't use it
							false:
							//Additional is not empty — use it
							true
						;
						
						if (
							//Additional property value is empty
							!$isAdditionalPropUsed &&
							//And source property value is empty too
							(
								//Empty object or array
								(
									$isSourcePropObjectOrArray &&
									count((array) $sourcePropValue) == 0
								) ||
								//Empty string
								(
									is_string($sourcePropValue) &&
									$sourcePropValue == ''
								) ||
								//NULL
								is_null($sourcePropValue)
							) &&
							//But they have different types
							$sourcePropValue !== $additionalPropValue
						){
							//Okay, overwrite source in this case
							$isAdditionalPropUsed = true;
						}
					}
					
					//If additional value must be used
					if ($isAdditionalPropUsed){
						if (
							//If recursive merging is needed
							$params->deep &&
							//And the value is an object or array
							$isAdditionalPropObjectOrArray
						){
							//Start recursion (`clone` must be called for all nested additional props, so recursion must be called even if `$sourcePropValue` is not an object or array)
							$additionalPropValue = self::extend([
								'objects' => [
									(
										$isSourcePropObjectOrArray ?
										$sourcePropValue :
										//If `$sourcePropValue` is not an array or object it isn't be used
										(
											//Type of resulting prop depends on `$additionalPropValue` type
											$isAdditionalPropObject ?
											new \stdClass() :
											[]
										)
									),
									$additionalPropValue
								],
								'deep' => true,
								'overwriteWithEmpty' => $params->overwriteWithEmpty
							]);
						}
						
						if (is_object($additionalPropValue)){
							$additionalPropValue = clone $additionalPropValue;
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
	 * unfold
	 * @version 1.1 (2021-11-17)
	 * 
	 * @see README.md
	 * 
	 * @return {stdClass|array}
	 */
	public static function unfold($params){
		$params = self::extend([
			'objects' => [
				//Defaults
				(object) [
					'keySeparator' => '.',
					'keyPrefix' => '',
					//The internal parameter, should not be used outside. Used only in child calls of recursion.
					'isSourceObject' => null
				],
				$params
			]
		]);
		
		//Array is used as base and it always be returned in child calls of recursion
		$result = [];
		
		$isSourceObject =
			//If it's the first call of recurson
			is_null($params->isSourceObject) ?
			//Use original type
			is_object($params->object) :
			//Use from parent call of recursion
			$params->isSourceObject
		;
		
		//Iterate over source
		foreach (
			$params->object as
			$key =>
			$value
		){
			//If the value must be unfolded
			if (
				(
					$isSourceObject &&
					is_object($value)
				) ||
				(
					!$isSourceObject &&
					is_array($value)
				)
			){
				$result = array_merge(
					$result,
					self::unfold([
						'object' => $value,
						'keyPrefix' =>
							$params->keyPrefix .
							$key .
							$params->keySeparator
						,
						'isSourceObject' => $isSourceObject
					])
				);
			//Если значение — не массив
			}else{
				//Запоминаем (в соответствии с ключом родителя)
				$result[$params->keyPrefix . $key] = $value;
			}
		}
		
		if (
			//If it's first call of recurson
			is_null($params->isSourceObject) &&
			//And the final result must be an object
			$isSourceObject
		){
			//Only the first call of recursion can return an object
			$result = (object) $result;
		}
		
		return $result;
	}
}