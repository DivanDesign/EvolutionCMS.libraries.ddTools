<?php
namespace DDTools;

class BaseClass {
	/**
	 * setExistingProps
	 * @version 1.3 (2019-12-14)
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
			$this->setProp([
				'object' => $this,
				'propName' => $propName,
				'propValue' => $propValue
			]);
		}
	}
	
	/**
	 * setProp
	 * @version 1.0 (2019-09-23)
	 * 
	 * @throws \ReflectionException
	 * @throws \Exception
	 * 
	 * @param $params {array_associative|stdClass} — The object of params. @required
	 * @param $params->object {object} — Объект для модификации. @required
	 * @param $params->propName {string} — Имя поля. @required
	 * @param $params->propValue {mixed} — Значение. @required
	 * @param $params->class {string|object|null} — Класс или объект. Default: null.
	 * 
	 * @return {void}
	 */
	private function setProp($params){
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
				$this->setProp([
					'object' => $params->object,
					'propName' => $params->propName,
					'propValue' => $params->propValue,
					'class' => $parent->getName()
				]);
			}else{
				throw new \Exception('Property “' . $params->propName . '” not found');
			}
		}
	}
	
	/**
	 * createChildInstance
	 * @version 1.1.1 (2019-08-22)
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
				'capitalizeName' => true
			],
			(array) $params
		);
		
		$thisClassName = get_called_class();
		
		$thisNameSpace = substr(
			$thisClassName,
			0,
			strrpos(
				$thisClassName,
				'\\'
			)
		);
		
		//Current classname without namespace
		$thisClassName = substr(
			$thisClassName,
			strrpos(
				$thisClassName,
				'\\'
			) + 1
		);
		
		//Capitalize child name if needed
		if ($params->capitalizeName){
			$params->name = ucfirst(strtolower($params->name));
		}
		
		$filePath =
			$params->parentDir .
			DIRECTORY_SEPARATOR .
			$params->name .
			DIRECTORY_SEPARATOR .
			$thisClassName .
			'.php'
		;
		
		if(is_file($filePath)){
			require_once($filePath);
			
			$objectClass =
				'\\' .
				$thisNameSpace .
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