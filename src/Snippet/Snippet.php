<?php
namespace DDTools;

abstract class Snippet {
	/**
	 * @property $name {string} — Constructor fills it from namespace.
	 */
	protected $name = '';
	
	/**
	 * @property $version {string} — Set in children classes.
	 */
	protected $version = '';
	
	/**
	 * @property $paths {stdClass}
	 * @property $paths->snippet {string} — Full path to the snippet folder.
	 * @property $paths->src {string} — Ful path to `src`.
	 */
	protected $paths = [
		'snippet' => '/',
		'src' => 'src/'
	];
	
	/**
	 * @property $params {stdClass} — Overwrite with defaults in children classes.
	 */
	protected $params = [];
	
	/**
	 * @property $paramsTypes {arrayAssociative} — Overwrite in child classes if you want to convert some parameters types.
	 * @property $paramsTypes[$paramName] {'integer'|'float'|'boolean'|'objectAuto'|'objectStdClass'|'objectArray'|'stringJsonAuto'|'stringJsonObject'|'stringJsonArray'} — The parameter type.
	 */
	protected $paramsTypes = [];
	
	/**
	 * @property $renamedParamsCompliance {arrayAssociative} — Overwrite in child classes if you want to rename some parameters with backward compatibility (see \ddTools::verifyRenamedParams).
	 */
	protected $renamedParamsCompliance = [];
	
	/**
	 * __construct
	 * @version 1.1.3 (2024-12-03)
	 * 
	 * @param $params {stdClass|arrayAssociative|stringJsonObject|stringQueryFormatted}
	 */
	public function __construct($params = []){
		// # Prepare name
		$thisClassName = get_called_class();
		
		// Get snippet name from namespace
		$this->name = substr(
			$thisClassName,
			0,
			strrpos(
				$thisClassName,
				'\\'
			)
		);
		
		
		// # Prepare paths
		$this->paths = (object) $this->paths;
		
		$this->paths->snippet =
			// Path to `assets`
			dirname(
				__DIR__,
				4
			)
			. '/snippets/'
			. $this->name
			. $this->paths->snippet
		;
		
		$this->paths->src =
			$this->paths->snippet
			. $this->paths->src
		;
		
		
		// # Prepare params
		$this->prepareParams($params);
	}
	
	/**
	 * prepareParams
	 * @version 1.2 (2025-04-28)
	 * 
	 * @param $params {stdClass|arrayAssociative|stringJsonObject|stringQueryFormatted}
	 * 
	 * @return {void}
	 */
	protected function prepareParams($params = []){
		$this->params = (object) $this->params;
		
		$params = \DDTools\Tools\Objects::convertType([
			'object' => $params,
			'type' => 'objectStdClass'
		]);
		
		// Renaming params with backward compatibility
		if (!empty($this->renamedParamsCompliance)){
			$params = \ddTools::verifyRenamedParams([
				'params' => $params,
				'compliance' => $this->renamedParamsCompliance,
				'returnCorrectedOnly' => false
			]);
		}
		
		if (!empty($this->paramsTypes)){
			foreach (
				$this->paramsTypes
				as $paramName
				=> $paramType
			){
				$paramType = strtolower($paramType);
				
				// Convert defaults
				if (
					\DDTools\Tools\Objects::isPropExists([
						'object' => $this->params,
						'propName' => $paramName
					])
				){
					if ($paramType == 'integer'){
						$this->params->{$paramName} = intval($this->params->{$paramName});
					}elseif ($paramType == 'float'){
						$this->params->{$paramName} = floatval($this->params->{$paramName});
					}elseif ($paramType == 'boolean'){
						$this->params->{$paramName} = boolval($this->params->{$paramName});
					}else{
						$this->params->{$paramName} = \DDTools\Tools\Objects::convertType([
							'object' => $this->params->{$paramName},
							'type' => $paramType
						]);
					}
				}
				
				// Convert given
				if (
					\DDTools\Tools\Objects::isPropExists([
						'object' => $params,
						'propName' => $paramName
					])
				){
					if ($paramType == 'integer'){
						$params->{$paramName} = intval($params->{$paramName});
					}elseif ($paramType == 'float'){
						$params->{$paramName} = floatval($params->{$paramName});
					}elseif ($paramType == 'boolean'){
						$params->{$paramName} = boolval($params->{$paramName});
					}else{
						$params->{$paramName} = \DDTools\Tools\Objects::convertType([
							'object' => $params->{$paramName},
							'type' => $paramType
						]);
					}
				}
			}
		}
		
		$this->params = \DDTools\Tools\Objects::extend([
			'objects' => [
				// Defaults
				$this->params,
				// Given parameters
				$params
			]
		]);
	}
	
	public abstract function run();
	
	/**
	 * runSnippet
	 * @version 1.0.3 (2024-12-03)
	 * 
	 * @param $params {stdClass|arrayAssociative|stringJsonObject|stringQueryFormatted}
	 * @param $params->name {string}
	 * @param $params->params {object}
	 * 
	 * @return $result {mixed} — Result of the snippet as is.
	 * @return $result {''} — Empty string if snippet is not exists.
	 */
	public static function runSnippet($params){
		$result = '';
		
		$params = \DDTools\Tools\Objects::extend([
			'objects' => [
				// Defaults
				(object) [
					'name' => '',
					'params' => []
				],
				\DDTools\Tools\Objects::convertType([
					'object' => $params,
					'type' => 'objectStdClass'
				])
			]
		]);
		
		$requireData = (object) [
			'snippetDir' =>
				// Path to `assets`
				dirname(
					__DIR__,
					4
				)
				. '/snippets/'
				. $params->name
				. '/'
			,
			'snippetFile' => 'src/Snippet.php',
			'requireFile' => 'require.php'
		];
		
		$requireData->snippetFile =
			$requireData->snippetDir
			. $requireData->snippetFile
		;
		$requireData->requireFile =
			$requireData->snippetDir
			. $requireData->requireFile
		;
		
		if (file_exists($requireData->snippetFile)){
			require_once($requireData->requireFile);
			
			$snippetClass =
				'\\'
				. $params->name
				. '\Snippet'
			;
			
			$snippetObject = new $snippetClass($params->params);
			
			$result = $snippetObject->run();
		}
		
		return $result;
	}
}