<?php
namespace DDTools;

abstract class Snippet {
	protected
		/**
		 * @property $name {string} — Constructor fills it from namespace.
		 */
		$name = '',
		
		/**
		 * @property $version {string} — Set in children classes.
		 */
		$version = '',
		
		/**
		 * @property $paths {stdClass}
		 * @property $paths->snippet {string} — Full path to the snippet folder.
		 * @property $paths->src {string} — Ful path to `src`.
		 */
		$paths = [
			'snippet' => '/',
			'src' => 'src/'
		],
		
		/**
		 * @property $params {stdClass} — Overwrite with defaults in children classes.
		 */
		$params = []
	;
	
	/**
	 * __construct
	 * @version 1.0 (2021-02-18)
	 * 
	 * @param $params {stdClass|arrayAssociative|stringJsonObject|stringQueryFormatted}
	 */
	public function __construct($params = []){
		//# Prepare name
		$thisClassName = get_called_class();
		
		//Get snippet name from namespace
		$this->name = substr(
			$thisClassName,
			0,
			strrpos(
				$thisClassName,
				'\\'
			)
		);
		
		
		//# Prepare paths
		$this->paths = (object) $this->paths;
		
		$this->paths->snippet =
			//path to `assets`
			dirname(
				__DIR__,
				4
			) .
			'/snippets/' .
			$this->name .
			$this->paths->snippet
		;
		
		$this->paths->src =
			$this->paths->snippet .
			$this->paths->src
		;
		
		
		//# Prepare params
		$this->params = \DDTools\ObjectTools::extend([
			'objects' => [
				//Defaults
				(object) $this->params,
				//Given parameters
				\DDTools\ObjectTools::convertType([
					'object' => $params,
					'type' => 'objectStdClass'
				])
			]
		]);
	}
	
	public abstract function run();
	
	/**
	 * runSnippet
	 * @version 1.0 (2021-02-18)
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
		
		$params = \DDTools\ObjectTools::extend([
			'objects' => [
				//Defaults
				(object) [
					'name' => '',
					'params' => []
				],
				\DDTools\ObjectTools::convertType([
					'object' => $params,
					'type' => 'objectStdClass'
				])
			]
		]);
		
		$requireData = (object) [
			'snippetDir' =>
				//path to `assets`
				dirname(
					__DIR__,
					4
				) .
				'/snippets/' .
				$params->name .
				'/'
			,
			'snippetFile' => 'src/Snippet.php',
			'requireFile' => 'require.php'
		];
		
		$requireData->snippetFile =
			$requireData->snippetDir .
			$requireData->snippetFile
		;
		$requireData->requireFile =
			$requireData->snippetDir .
			$requireData->requireFile
		;
		
		if (file_exists($requireData->snippetFile)){
			require_once($requireData->requireFile);
			
			$snippetClass =
				'\\' .
				$params->name .
				'\Snippet'
			;
			
			$snippetObject = new $snippetClass($params->params);
			
			$result = $snippetObject->run();
		}
		
		return $result;
	}
}