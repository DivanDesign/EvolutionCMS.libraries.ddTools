<?php
namespace DDTools;

class ObjectCollection {
	protected
		/**
		 * @property $items {array}
		 * @property $items[$itemIndex] {array|object}
		 */
		$items = []
	;
	
	/**
	 * __construct
	 * @version 1.0 (2021-11-26)
	 * 
	 * @see README.md
	 */
	public function __construct($params = []){
		$this->setItems($params);
	}
	
	/**
	 * setItems
	 * @version 1.0 (2021-11-26)
	 * 
	 * @see README.md
	 */
	public function setItems($params = []){
		//Reset items
		$this->items = [];
		
		//Add new items
		$this->addItems($params);
	}
	
	/**
	 * addItems
	 * @version 1.2 (2021-12-02)
	 * 
	 * @see README.md
	 */
	public function addItems($params = []){
		//# Prepare params
		$params = \DDTools\ObjectTools::extend([
			'objects' => [
				//Defaults
				(object) [
					'items' => null,
					'itemType' => null
				],
				$params
			]
		]);
		
		
		//# Run
		if (!is_null($params->items)){
			//Items must be an array
			if (!is_array($params->items)){
				$params->items = \DDTools\ObjectTools::convertType([
					'object' => $params->items,
					'type' => 'objectArray'
				]);
			}
			
			//Reset keys because they are no needed
			$params->items = array_values($params->items);
			
			//If need to convert type of items
			if (!is_null($params->itemType)){
				foreach (
					$params->items as
					$itemIndex =>
					$itemObject
				){
					$params->items[$itemIndex] = \DDTools\ObjectTools::convertType([
						'object' => $itemObject,
						'type' => $params->itemType
					]);
				}
			}
			
			$this->items = array_merge(
				$this->items,
				$params->items
			);
		}
	}
	
	/**
	 * convertItemsType
	 * @version 1.0 (2021-12-02)
	 * 
	 * @see README.md
	 */
	public function convertItemsType($params = []){
		//# Prepare params
		$params = \DDTools\ObjectTools::extend([
			'objects' => [
				//Defaults
				(object) [
					'filter' => '',
					'itemType' => 'objectStdClass'
				],
				$params
			]
		]);
		
		$params->filter = $this->prepareItemsFilter($params->filter);
		
		
		//# Run
		foreach (
			$this->items as
			$itemIndex =>
			$itemObject
		){
			if (
				//If item is matched to filter
				$this->isItemMatchFilter([
					'item' => $itemObject,
					'filter' => $params->filter
				])
			){
				$this->items[$itemIndex] = \DDTools\ObjectTools::convertType([
					'object' => $itemObject,
					'type' => $params->itemType
				]);
			}
		}
	}
	
	/**
	 * updateItems
	 * @version 1.0 (2021-12-02)
	 * 
	 * @see README.md
	 */
	public function updateItems($params = []){
		//# Prepare params
		$params = \DDTools\ObjectTools::extend([
			'objects' => [
				//Defaults
				(object) [
					'filter' => '',
					'data' => [],
					'limit' => 0
				],
				$params
			]
		]);
		
		$params->filter = $this->prepareItemsFilter($params->filter);
		
		
		//# Run
		$affectedCount = 0;
		
		foreach (
			$this->items as
			$itemIndex =>
			$itemObject
		){
			if (
				//If item is matched to filter
				$this->isItemMatchFilter([
					'item' => $itemObject,
					'filter' => $params->filter
				])
			){
				$this->items[$itemIndex] = \DDTools\ObjectTools::extend([
					'objects' => [
						$itemObject,
						$params->data
					]
				]);
				
				//Increment result count
				$affectedCount++;
				
				//If next item is no needed
				if ($affectedCount == $params->limit){
					//Stop the cycle
					break;
				}
			}
		}
	}
	
	/**
	 * getItems
	 * @version 1.0 (2021-12-01)
	 * 
	 * @see README.md
	 */
	public function getItems($params = []){
		//# Prepare params
		$params = \DDTools\ObjectTools::extend([
			'objects' => [
				//Defaults
				(object) [
					'filter' => '',
					'maxResults' => 0,
					'propAsResultKey' => null,
					'propAsResultValue' => null
				],
				$params
			]
		]);
		
		$params->filter = $this->prepareItemsFilter($params->filter);
		
		
		//# Run
		$result = [];
		$resultCount = 0;
		
		//Filter items
		foreach (
			$this->items as
			$itemObject
		){
			if (
				//If item is matched to filter
				$this->isItemMatchFilter([
					'item' => $itemObject,
					'filter' => $params->filter
				])
			){
				//Save only field value instead of object if needed
				if (!is_null($params->propAsResultValue)){
					$resultItemObject = \DDTools\ObjectTools::getPropValue([
						'object' => $itemObject,
						'propName' => $params->propAsResultValue
					]);
				}else{
					$resultItemObject = $itemObject;
				}
				
				//Save item
				if (!is_null($params->propAsResultKey)){
					$result[
						\DDTools\ObjectTools::getPropValue([
							'object' => $itemObject,
							'propName' => $params->propAsResultKey
						])
					] = $resultItemObject;
				}else{
					$result[] = $resultItemObject;
				}
				
				//Increment result count
				$resultCount++;
				
				//If next item is no needed
				if ($resultCount == $params->maxResults){
					//Stop the cycle
					break;
				}
			}
		}
		
		return $result;
	}
	
	/**
	 * getOneItem
	 * @version 1.0 (2021-12-01)
	 * 
	 * @see README.md
	 */
	public function getOneItem($params = []){
		//# Prepare params
		$params = \DDTools\ObjectTools::extend([
			'objects' => [
				//Defaults
				(object) [
					'filter' => '',
					'notFoundResult' => null
				],
				$params
			]
		]);
		
		
		//# Run
		$result = $this->getItems([
			'filter' => $params->filter,
			'maxResults' => 1
		]);
		
		if (!empty($result)){
			$result = $result[0];
		}else{
			$result = $params->notFoundResult;
		}
		
		return $result;
	}
	
	/**
	 * isItemMatchFilter
	 * @version 1.0 (2021-12-01)
	 * 
	 * @param $params {array} — Parameters, the pass-by-name style is used. @required
	 * @param $params->item {array|object} — An item to test. @required
	 * @param $params->filter {array} — Result of $this->prepareItemsFilter. @required
	 * 
	 * @return $result {boolean}
	 */
	private function isItemMatchFilter($params){
		$params = (object) $params;
		
		//By default assume that item is matched
		$result = true;
		
		//Iterate over “or” conditions
		foreach (
			$params->filter as
			$orCondition
		){
			//Iterate over “and” conditions
			foreach (
				$orCondition as
				$andCondition
			){
				//If the item has no the property
				if (
					!\DDTools\ObjectTools::isPropExists([
						'object' => $params->item,
						'propName' => $andCondition->propName
					])
				){
					$result = false;
				//If filtration by the property value is no needed
				}elseif ($andCondition->operator == 'isset'){
					$result = true;
				//==
				}elseif ($andCondition->operator == '=='){
					$result =
						\DDTools\ObjectTools::getPropValue([
							'object' => $params->item,
							'propName' => $andCondition->propName
						]) ==
						$andCondition->propValue
					;
				//!=
				}else{
					$result =
						\DDTools\ObjectTools::getPropValue([
							'object' => $params->item,
							'propName' => $andCondition->propName
						]) !=
						$andCondition->propValue
					;
				}
				
				//If item is not matched to this “and” condition, check with next “or”
				if (!$result){
					break;
				}
			}
			
			//If the item is matched to all “and” conditions, so it's already success
			if ($result){
				break;
			}
		}
		
		return $result;
	}
	
	/**
	 * prepareItemsFilter
	 * @version 1.0 (2021-12-01)
	 * 
	 * @param $filter {string}
	 * 
	 * @return $result {array} — “Or” conditions
	 * @return $result[$orIndex] {array} — “And” conditions.
	 * @return $result[$orIndex][$andIndex] {stdClass} — A condition. @required
	 * @return $result[$orIndex][$andIndex]->operator {'isset'|'=='|'!='} — A condition operator. @required
	 * @return $result[$orIndex][$andIndex]->propName {string} — A condition property name. @required
	 * @return $result[$orIndex][$andIndex]->propValue {string} — A condition property value if exists. Default: —.
	 */
	private function prepareItemsFilter($filter = ''){
		$result = [];
		
		if (!empty($filter)){
			//Explode “or” conditions
			$filter = explode(
				'||',
				$filter
			);
			
			//Iterate over “or” conditions
			foreach (
				$filter as
				$orIndex =>
				$orCondition
			){
				$result[$orIndex] = [];
				
				//Iterate over “and” conditions
				foreach (
					//Explode “and” conditions
					explode(
						'&&',
						$orCondition
					) as
					$andIndex =>
					$andCondition
				){
					//Condition object
					$result[$orIndex][$andIndex] = (object) [
						'operator' => 'isset',
						'propName' => ''
					];
					
					//Prepare operator
					if (
						strpos(
							$andCondition,
							'=='
						) !== false
					){
						$result[$orIndex][$andIndex]->operator = '==';
					}else if(
						strpos(
							$andCondition,
							'!='
						) !== false
					){
						$result[$orIndex][$andIndex]->operator = '!=';
					}
					
					//Prepare condition
					if ($result[$orIndex][$andIndex]->operator == 'isset'){
						//Just save name
						$result[$orIndex][$andIndex]->propName = $andCondition;
					}else{
						//Explode to name and value
						$andCondition =	explode(
							$result[$orIndex][$andIndex]->operator,
							$andCondition
						);
						
						//Save name
						$result[$orIndex][$andIndex]->propName = $andCondition[0];
						
						//Prepare and save value
						$result[$orIndex][$andIndex]->propValue = trim(
							//Trim whitespaces
							trim($andCondition[1]),
							//Then trim quotes
							'"\''
						);
					}
					
					//Prepare name
					$result[$orIndex][$andIndex]->propName = trim(
						//Trim whitespaces
						trim($result[$orIndex][$andIndex]->propName),
						//Then trim quotes
						'"\''
					);
				}
			}
		}
		
		return $result;
	}
	
	/**
	 * count
	 * @version 1.0 (2021-11-30)
	 * 
	 * @see README.md
	 */
	public function count(){
		return count($this->items);
	}
}
?>