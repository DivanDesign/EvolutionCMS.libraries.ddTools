<?php
namespace DDTools\Base;

trait AncestorTrait {
	/**
	 * getChildClassName
	 * @version 1.0 (2024-02-06)
	 * 
	 * @see README.md
	 * 
	 * @throws \Exception
	 */
	final public static function getChildClassName($params): string {
		//Defaults
		$params = (object) array_merge(
			[
				'parentDir' => null,
				'capitalizeName' => true,
			],
			(array) $params
		);
		
		//Capitalize child name if needed
		if ($params->capitalizeName){
			$params->name = ucfirst(strtolower($params->name));
		}
		
		$thisClassNameFull = get_called_class();
		
		if (empty($params->parentDir)){
			$objectClassReflector = new \ReflectionClass($thisClassNameFull);
			
			$params->parentDir = dirname($objectClassReflector->getFileName());
		}
		
		$thisNameSpace = substr(
			$thisClassNameFull,
			0,
			strrpos(
				$thisClassNameFull,
				'\\'
			)
		);
		
		//Current classname without namespace
		$thisClassName = substr(
			$thisClassNameFull,
			(
				strrpos(
					$thisClassNameFull,
					'\\'
				)
				+ 1
			)
		);
		
		$filePath =
			$params->parentDir
			. DIRECTORY_SEPARATOR
			. $params->name
			. DIRECTORY_SEPARATOR
			. $thisClassName
			. '.php'
		;
		
		if(is_file($filePath)){
			require_once($filePath);
			
			return
				'\\'
				. $thisNameSpace
				. '\\'
				. $params->name
				. '\\'
				. $thisClassName
			;
		}else{
			throw new \Exception(
				$thisClassName . ' “' . $params->name . '” not found.',
				500
			);
		}
	}
	
	/**
	 * createChildInstance
	 * @version 1.2.2 (2024-02-06)
	 * 
	 * @see README.md
	 */
	final public static function createChildInstance($params){
		$params = (object) $params;
		
		$objectClass = static::getChildClassName($params);
		
		return new $objectClass(
			\DDTools\ObjectTools::isPropExists([
				'object' => $params,
				'propName' => 'params',
			])
			? $params->params
			: []
		);
	}
}