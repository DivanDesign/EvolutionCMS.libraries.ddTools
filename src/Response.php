<?php
namespace DDTools;


abstract class Response
{
	protected $meta, $data;
	
	/**
	 * validateMeta
	 * 
	 * Validates the “meta” part of a response.
	 * 
	 * @return bool
	 */
	abstract public function validateMeta();
	
	/**
	 * setMeta
	 * 
	 * Setter for $this->meta.
	 * 
	 * @param $meta
	 * 
	 * @return bool
	 */
	public function setMeta($meta){
		$output = false;
		
		if($this->validateMeta()){
			$this->meta = $meta;
			$output = true;
		}
		
		return $output;
	}
	
	/**
	 * toArray
	 * 
	 * Converts this object to array.
	 * 
	 * @return array
	 */
	public function toArray(){
		return array(
			'meta' => $this->meta,
			'data' => $this->data
		);
	}
	
	/**
	 * toJSON
	 * 
	 * Converts this object to JSON string.
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