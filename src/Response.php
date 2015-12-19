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
	 * @param $meta
	 * 
	 * @return bool
	 */
	abstract public function validateMeta(array $meta);
	
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
		
		if($this->validateMeta($meta)){
			$this->meta = $meta;
			$output = true;
		}
		
		return $output;
	}
	
	/**
	 * getMeta
	 * 
	 * Getter for $this->meta
	 * 
	 * @return null|array
	 */
	public function getMeta(){
		return $this->meta;
	}
	
	/**
	 * setData
	 * 
	 * Setter for $this->data.
	 * 
	 * @param $data
	 */
	public function setData($data){
		$this->data = $data;
	}
	
	/**
	 * getData
	 * 
	 * Getter for $this->data.
	 * 
	 * @return mixed
	 */
	public function getData(){
		return $this->data;
	}
	
	/**
	 * toArray
	 * 
	 * Converts this object to array.
	 * 
	 * @return array
	 */
	public function toArray(){
		$output = array(
			'meta' => $this->meta
		);
		
		if(isset($this->data)){
			$output['data'] = $this->data;
		}
		
		return $output;
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