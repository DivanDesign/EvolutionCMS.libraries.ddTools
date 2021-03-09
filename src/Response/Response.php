<?php
namespace DDTools;

class Response {
	protected static
		/**
		 * @property $allowedMetaKeys {array} — Allowed keys in $this->meta.
		 */
		$allowedMetaKeys = [
			'code',
			'eTag',
			'success',
			'message'
		],
		/**
		 * @property $allowedMetaMessageKeys {array} — Allowed keys in $this->meta['message'].
		 */
		$allowedMetaMessageKeys = [
			'content',
			'title'
		],
		/**
		 * @property $requiredMetaKeys {array} — Required keys in $this->meta.
		 */
		$requiredMetaKeys = [
			'code',
			'success'
		],
		/**
		 * @property $requiredMetaMessageKeys {array} — Required keys in $this->meta['message'].
		 */
		$requiredMetaMessageKeys = [
			'content'
		]
	;
	
	protected
		$meta,
		$data
	;
	
	/**
	 * validateMeta
	 * @version 1.1.1 (2021-03-10)
	 * 
	 * @param $meta {arrayAssociative} — Is an array of meta data. The method excludes any values passed in $meta except “code”, “eTag”, “success”,
	 * and “message”. $meta['code'] and $meta['success'] are required. If defined, $meta['message'] must be an associative array with content
	 * and, optionally, with a title.
	 * 
	 * Examples:
	 *
	 * ```php
	 * $meta = [
	 * 		"code" => 200, // REQUIRED
	 * 		"success" => true // REQUIRED
	 * ];
	 * 
	 * $meta = [
	 * 		"code" => 201, // REQUIRED
	 * 		"success" => true, // REQUIRED
	 * 		"message" => [
	 * 			"content" => "You have successfully signed up. You will be redirected to your account in a moment.", // REQUIRED
	 * 			"title" => "Success!"
	 * 		]
	 * ];
	 * ```
	 * 
	 * @return {boolean}
	 */
	public function validateMeta($meta){
		$result = false;
		
		//Parameter is valid
		if (is_array($meta)){
			$paramKeys = array_keys($meta);
			
			if(
				//All required items are set
				!count(array_diff(
					static::$requiredMetaKeys,
					$paramKeys
				)) &&
				//And only allowed items are set
				!count(array_diff(
					$paramKeys,
					static::$allowedMetaKeys
				)) &&
				//code is int
				is_int($meta['code']) &&
				//success is bool
				is_bool($meta['success']) &&
				(
					//message is not set
					!isset($meta['message']) ||
					//Or is valid
					$this->validateMetaMessage($meta['message'])
				)
			){
				$result = true;
			}
		}
		
		return $result;
	}
	
	/**
	 * validateMetaMessage
	 * @version 1.0.2 (2021-03-10)
	 * 
	 * @param $message {arrayAssociative} — @reuired
	 * @param $message['content'] {string} — @required
	 * @param $message['title'] {string}
	 * 
	 * @return {boolean}
	 */
	public function validateMetaMessage($message){
		$result = false;
		
		//Parameter is valid
		if (is_array($message)){
			$paramKeys = array_keys($message);
			
			if (
				//All required items are set
				!count(array_diff(
					static::$requiredMetaMessageKeys,
					$paramKeys
				)) &&
				//And only allowed items are set
				!count(array_diff(
					$paramKeys,
					static::$allowedMetaMessageKeys
				))
			){
				$result = true;
			}
		}
		
		return $result;
	}
	
	/**
	 * setMeta
	 * @version 1.0.1 (2021-03-10)
	 * 
	 * @desc Setter for $this->meta.
	 * 
	 * @param $meta
	 * 
	 * @return {boolean}
	 */
	public function setMeta($meta){
		$result = false;
		
		if($this->validateMeta($meta)){
			$this->meta = $meta;
			$result = true;
		}
		
		return $result;
	}
	
	/**
	 * getMeta
	 * 
	 * @desc Getter for $this->meta
	 * 
	 * @return {null|array}
	 */
	public function getMeta(){
		return $this->meta;
	}
	
	/**
	 * setData
	 * 
	 * @desc Setter for $this->data.
	 * 
	 * @param $data
	 */
	public function setData($data){
		$this->data = $data;
	}
	
	/**
	 * getData
	 * 
	 * @desc Getter for $this->data.
	 * 
	 * @return {mixed}
	 */
	public function getData(){
		return $this->data;
	}
	
	/**
	 * toArray
	 * @version 1.0.2 (2021-03-10)
	 * 
	 * @desc Converts this object to array.
	 * 
	 * @return {array}
	 */
	public function toArray(){
		$result = [
			'meta' => $this->meta
		];
		
		if(isset($this->data)){
			$result['data'] = $this->data;
		}
		
		return $result;
	}
	
	/**
	 * toJSON
	 * @version 1.0.1 (2021-03-10)
	 * 
	 * @desc Converts this object to JSON string.
	 * 
	 * @return string
	 */
	public function toJSON(){
		return \DDTools\ObjectTools::convertType([
			'object' => $this->toArray(),
			'type' => 'stringJsonObject'
		]);
	}
	
	public function __toString(){
		return $this->toJSON();
	}
}