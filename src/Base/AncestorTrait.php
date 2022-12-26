<?php
namespace DDTools\Base;

trait AncestorTrait {
	/**
	 * createChildInstance
	 * @version 1.1.1 (2019-08-22)
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