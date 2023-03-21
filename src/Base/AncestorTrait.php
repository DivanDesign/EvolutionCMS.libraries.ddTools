<?php
namespace DDTools\Base;

trait AncestorTrait {
	/**
	 * createChildInstance
	 * @version 1.2 (2023-03-21)
	 * 
	 * @see README.md
	 * 
	 * @throws \Exception
	 */
	public static final function createChildInstance($params){
		//Defaults
		$params = (object) array_merge(
			[
				'params' => [],
				'parentDir' => null,
				'capitalizeName' => true
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
			strrpos(
				$thisClassNameFull,
				'\\'
			) + 1
		);
		
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