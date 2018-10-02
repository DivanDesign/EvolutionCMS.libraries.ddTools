<?php
namespace DDTools;


class FilesTools
{
	/**
	 * copyDir
	 * @version 2.0 (2018-10-01)
	 * 
	 * @desc Copies a required folder with all contents recursively.
	 * 
	 * @param $params {array_associative|stdClass} — The object of params. @required
	 * @param $params['sourcePath'] {string} — Path to the directory, that should copied. @required
	 * @param $params['destinationPath'] {string} — The destination path. @required
	 * 
	 * @return {boolean} — Returns true on success or false on failure.
	 */
	public static function copyDir($params){
		$params = (object) $params;
		
		//Допишем папкам недостающие '/' при необходимости
		if (substr(
			$params->sourcePath,
			-1
		) != '/'){
			$params->sourcePath .= '/';
		}
		if (substr(
			$params->destinationPath,
			-1
		) != '/'){
			$params->destinationPath .= '/';
		}
		
		//Проверяем существование
		if (!file_exists($params->sourcePath)){return false;}
		//Если папки назначения нет, создадим её
		if (!file_exists($params->destinationPath)){mkdir($params->destinationPath);}
		
		//Получаем файлы в директории
		$fileNames = array_diff(
			scandir($params->sourcePath),
			[
				'.',
				'..'
			]
		);
		
		foreach ($fileNames as $fileName){
			//Если это папка, обработаем её
			if (is_dir($params->sourcePath.$fileName)){
				self::copyDir(
					$params->sourcePath.$fileName,
					$params->destinationPath.$fileName
				);
			}else{
				copy(
					$params->sourcePath.$fileName,
					$params->destinationPath.$fileName
				);
			}
		}
		
		return true;
	}
	
	/**
	 * removeDir
	 * @version 1.0.5 (2018-10-01)
	 * 
	 * @desc Removes a required folder with all contents recursively.
	 * 
	 * @param $path {string} — Path to the directory, that should removed. @required
	 * 
	 * @return {boolean}
	 */
	public static function removeDir($path){
		//Если не существует, ок
		if (!file_exists($path)){return true;}
		
		//Получаем файлы в директории
		$fileNames = array_diff(
			scandir($path),
			[
				'.',
				'..'
			]
		);
		
		foreach ($fileNames as $fileName){
			//Если это папка, обработаем её
			if (is_dir($path.'/'.$fileName)){
				self::removeDir($path.'/'.$fileName);
			}else{
				unlink($path.'/'.$fileName);
			}
		}
		
		return rmdir($path);
	}
}