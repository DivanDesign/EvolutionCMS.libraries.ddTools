<?php
namespace DDTools\Base;

abstract class Base {
	/**
	 * @var $ddClassNames {\stdClass} — Storage of all class names.
	 * @var $ddClassNames->{$className} {\stdClass}
	 * @var $ddClassNames->{$className}->full {string} — Full class name including namespace, e. g.: '\\ddSendFeedback\\Sender\\Email\\Sender'.
	 * @var $ddClassNames->{$className}->nameShort {string} — Short class name, e. g.: 'Sender'.
	 * @var $ddClassNames->{$className}->namespaceFull {string} — Namespace, e. g.: '\\ddSendFeedback\\Sender\\Email'.
	 * @var $ddClassNames->{$className}->namespaceShort {string} — Last namespace item, e. g.: 'Email'.
	 * @var $ddClassNames->{$className}->namespacePrefix {string} — Namespace prefix, e. g.: '\\ddSendFeedback\\Sender'.
	 */
	private static $ddClassNames = null;
	
	/**
	 * getClassName
	 * @version 1.1 (2024-03-27)
	 * 
	 * @see README.md
	 * 
	 * @return $result {stdClass}
	 */
	public static function getClassName(?string $classNameFull = null): \stdClass {
		$classNameFull =
			is_null($classNameFull)
			? get_called_class()
			: ltrim($classNameFull, '\\')
		;
		
		// Init
		if (is_null(self::$ddClassNames)){
			self::$ddClassNames = new \stdClass();
		}
		
		// If not defined before for this child class
		if (
			!property_exists(
				self::$ddClassNames,
				$classNameFull
			)
		){
			static::$ddClassNames->{$classNameFull} = (object) [
				'full' => '',
				'nameShort' => '',
				'namespaceFull' => '',
				'namespaceShort' => '',
				'namespacePrefix' => '',
			];
			
			static::$ddClassNames->{$classNameFull}->full = $classNameFull;
			
			$fullArray = explode(
				'\\',
				static::$ddClassNames->{$classNameFull}->full
			);
			
			static::$ddClassNames->{$classNameFull}->full = '\\' . static::$ddClassNames->{$classNameFull}->full;
			// Extract short class name
			static::$ddClassNames->{$classNameFull}->nameShort = array_pop($fullArray);
			
			// If namespace exists
			if (count($fullArray) > 0){
				static::$ddClassNames->{$classNameFull}->namespaceFull =
					'\\'
					. implode(
						'\\',
						$fullArray
					)
				;
				// Extract namespace
				static::$ddClassNames->{$classNameFull}->namespaceShort = array_pop($fullArray);
				
				// If neamespace prefix exists
				if (count($fullArray) > 0){
					static::$ddClassNames->{$classNameFull}->namespacePrefix =
						'\\'
						. implode(
							'\\',
							$fullArray
						)
					;
				}
			}
		}
		
		return static::$ddClassNames->{$classNameFull};
	}
	
	/**
	 * setExistingProps
	 * @version 1.4.2 (2024-12-03)
	 * 
	 * @see README.md
	 * 
	 * @return {void}
	 */
	public function setExistingProps($props){
		if (is_string($props)){
			$props = \DDTools\Tools\Objects::convertType([
				'object' => $props,
				'type' => 'objectStdClass'
			]);
		}
		
		foreach (
			$props
			as $propName
			=> $propValue
		){
			$this->ddSetProp([
				'object' => $this,
				'propName' => $propName,
				'propValue' => $propValue
			]);
		}
	}
	
	/**
	 * ddSetProp
	 * @version 1.0.3 (2024-08-04)
	 * 
	 * @throws \ReflectionException
	 * 
	 * @param $params {stdClass|arrayAssociative} — The parameters object. @required
	 * @param $params->object {object} — Объект для модификации. @required
	 * @param $params->propName {string} — Имя поля. @required
	 * @param $params->propValue {mixed} — Значение. @required
	 * @param $params->class {string|object|null} — Класс или объект. Default: null.
	 * 
	 * @return {void}
	 */
	private function ddSetProp($params){
		// Defaults
		$params = (object) array_merge(
			[
				'class' => null
			],
			(array) $params
		);
		
		if ($params->class === null){
			$params->class = get_class($params->object);
		}
		
		$classReflection = new \ReflectionClass($params->class);
		
		if ($classReflection->hasProperty($params->propName)){
			$reflectionProperty = $classReflection->getProperty($params->propName);
			
			if (!$reflectionProperty->isPublic()){
				$reflectionProperty->setAccessible(true);
			}
			
			$reflectionProperty->setValue(
				$params->object,
				$params->propValue
			);
		}else{
			$parent = $classReflection->getParentClass();
			
			if ($parent !== false){
				$this->ddSetProp([
					'object' => $params->object,
					'propName' => $params->propName,
					'propValue' => $params->propValue,
					'class' => $parent->getName()
				]);
			}
		}
	}
	
	/**
	 * ddGetPropValues
	 * @version 1.0.1 (2024-12-03)
	 * 
	 * @throws \ReflectionException
	 * 
	 * @param $class {string|object|null} — Класс или объект. Default: null.
	 * 
	 * @return $result {arrayAssociative}
	 * @return $result[$propName] {mixed}
	 */
	private function ddGetPropValues($class = null){
		$result = [];
		
		if ($class === null){
			$class = get_class($this);
		}
		
		$classReflection = new \ReflectionClass($class);
		$reflectionProperties = $classReflection->getProperties();
		
		if (!empty($reflectionProperties)){
			foreach(
				$reflectionProperties
				as $reflectionProperty
			){
				if (!$reflectionProperty->isPublic()){
					$reflectionProperty->setAccessible(true);
				}
				
				$result[$reflectionProperty->getName()] = $reflectionProperty->getValue($this);
			}
		}else{
			$parent = $classReflection->getParentClass();
			
			if ($parent !== false){
				$result = array_merge(
					$result,
					$this->ddGetPropValues($parent->getName())
				);
			}
		}
		
		return $result;
	}
	
	/**
	 * toArray
	 * @version 1.0 (2020-05-06)
	 * 
	 * @see README.md
	 */
	public function toArray(){
		return $this->ddGetPropValues();
	}
	
	/**
	 * toJSON
	 * @version 1.1.1 (2024-08-02)
	 * 
	 * @see README.md
	 * 
	 * @return {stringJsonObject}
	 */
	public function toJSON(){
		return \DDTools\Tools\Objects::convertType([
			'object' => $this->toArray(),
			'type' => 'stringJsonAuto'
		]);
	}
	
	/**
	 * __toString
	 * @version 1.0 (2020-05-06)
	 * 
	 * @see README.md
	 * 
	 * @return {stringJsonObject}
	 */
	public function __toString(){
		return $this->toJSON();
	}
}