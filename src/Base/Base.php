<?php
namespace DDTools\Base;

abstract class Base {
	/**
	 * setExistingProps
	 * @version 1.4 (2022-01-08)
	 * 
	 * @see README.md
	 * 
	 * @return {void}
	 */
	public function setExistingProps($props){
		if (is_string($props)){
			$props = \DDTools\ObjectTools::convertType([
				'object' => $props,
				'type' => 'objectStdClass'
			]);
		}
		
		foreach (
			$props as
			$propName =>
			$propValue
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
	 * @version 1.0.2 (2020-05-06)
	 * 
	 * @throws \ReflectionException
	 * 
	 * @param $params {stdClass|arrayAssociative} — Parameters, the pass-by-name style is used. @required
	 * @param $params->object {object} — Объект для модификации. @required
	 * @param $params->propName {string} — Имя поля. @required
	 * @param $params->propValue {mixed} — Значение. @required
	 * @param $params->class {string|object|null} — Класс или объект. Default: null.
	 * 
	 * @return {void}
	 */
	private function ddSetProp($params){
		//Defaults
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
	 * @version 1.0 (2020-05-06)
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
				$reflectionProperties as
				$reflectionProperty
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
	 * @version 1.1 (2022-12-26)
	 * 
	 * @see README.md
	 * 
	 * @return {stringJsonObject}
	 */
	public function toJSON(){
		return \DDTools\ObjectTools::convertType([
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