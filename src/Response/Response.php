<?php
namespace DDTools;

class Response {
	protected static
		/**
		 * allowedMetaKeys
		 * 
		 * Allowed keys in $this->meta.
		 * 
		 * @var array
		 */
		$allowedMetaKeys = [
			'code',
			'eTag',
			'success',
			'message'
		],
		/**
		 * allowedMetaMessageKeys
		 * 
		 * Allowed keys in $this->meta['message'].
		 * 
		 * @var array
		 */
		$allowedMetaMessageKeys = [
			'content',
			'title'
		]
	;
	
	protected
		$meta,
		$data
	;
	
	/**
	 * validateMeta
	 * @version 1.0.3 (2021-03-10)
	 * 
	 * @param array $meta - is an array of meta data. The method excludes any values passed in $meta except “code”, “eTag”, “success”,
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
	public function validateMeta(array $meta){
		$result = false;
		
		if(
			//code is set and int
			isset($meta['code']) &&
			is_int($meta['code']) &&
			//success is set and bool
			isset($meta['success']) &&
			is_bool($meta['success']) &&
			//there is no diff between meta keys and allowed meta keys
			!count(array_diff(
				array_keys($meta),
				static::$allowedMetaKeys
			)) &&
			(
				//message is not set
				!isset($meta['message']) ||
				(
					//message is set and contains content
					is_array($meta['message']) &&
					isset($meta['message']['content']) &&
					//there is no diff between meta message keys and allowed meta message keys
					!count(array_diff(
						array_keys($meta['message']),
						static::$allowedMetaMessageKeys
					))
				)
			)
		){
			$result = true;
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
	 * 
	 * @desc Converts this object to JSON string.
	 * 
	 * @return string
	 */
	public function toJSON(){
		return json_encode($this->toArray());
	}
	
	public function __toString(){
		return $this->toJSON();
	}
}