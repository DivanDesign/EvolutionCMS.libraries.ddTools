<?php
namespace DDTools\Storage;

abstract class Storage {
	use \DDTools\Base\AncestorTrait;
	
	/**
	 * items_add
	 * @version 1.0.1 (2024-08-04)
	 * 
	 * @param $params {stdClass|arrayAssociative} — The parameters object.
	 * @param $params->items {mixed} — An array of items.
	 * 	{array} — can be indexed or associative, keys will not be used
	 * 	{object} — also can be set as an object for better convenience, only property values will be used
	 * 	{stringJsonObject} — [JSON](https://en.wikipedia.org/wiki/JSON) object
	 * 	{stringJsonArray} — [JSON](https://en.wikipedia.org/wiki/JSON) array
	 * 	{stringHjsonObject} — [HJSON](https://hjson.github.io/) object
	 * 	{stringHjsonArray} — [HJSON](https://hjson.github.io/) array
	 * 	{stringQueryFormatted} — [Query string](https://en.wikipedia.org/wiki/Query_string)
	 * @param $params->items[$itemIndex] {object|array} — An item.
	 * @param $params->items[$itemIndex]->{$propName} {mixed} — Keys are property names, values are values.
	 * 
	 * @return $result {arrayIndexed} — Array of added items.
	 * @return $result[$itemIndex] {stdClass} — A item object.
	 * @return $result[$itemIndex]->id {integer} — ID of added item.
	 */
	abstract public function items_add($params): array;
	
	/**
	 * items_addOne
	 * @version 2.0.2 (2024-12-03)
	 * 
	 * @param $params {stdClass|arrayAssociative} — The parameters object.
	 * @param $params->data {object|array} — An item.
	 * @param $params->data->{$propName} {mixed} — Keys are property names, values are values.
	 * 
	 * @return $result {stdClass|null} — An added item object or `null` if fail.
	 * @return $result->id {integer} — ID of added item (if ID is used).
	 */
	public function items_addOne($params): ?\stdClass {
		$params = (object) $params;
		
		$result = $this->items_add([
			'items' => [
				$params->data,
			],
		]);
		
		if (!empty($result)){
			$result = $result[0];
		}else{
			$result = null;
		}
		
		return $result;
	}
	
	/**
	 * items_update
	 * @version 1.6 (2024-08-06)
	 * 
	 * @param $params {stdClass|arrayAssociative} — The parameters object.
	 * @param $params->data {object|array} — New item data. Existing item will be extended by this data.
	 * @param $params->data->{$propName} {mixed} — Keys are property names, values are values.
	 * @param [$params->where=''] {stdClass|arrayAssociative|string|null} — SQL 'WHERE' clause. null or '' means that all items will be updated.
	 * @param $params->where->{$propName} {string|arrayIndexed} — Key is an item property name, value is a value. Only valid property names will be used, others will be ignored. You can specify multiple value variants through an array (SQL IN() operator), please note that empty arrays will just be ignored.
	 * @param $params->where->{$propName}[$i] {string} — A value.
	 * @param [$params->limit=0] {integer|0} — Maximum number of items to delete. `0` means all matching.
	 * @param [$params->offset=0] {integer} — Offset of the first item (can be useful with $params->limit).
	 * 
	 * @return $result {arrayIndexed} — Array of updated items.
	 * @return $result[$itemIndex] {stdClass} — A item object.
	 * @return $result[$itemIndex]->id {integer} — ID of added item.
	 */
	abstract public function items_update($params): array;
	
	/**
	 * items_updateOne
	 * @version 1.2.2 (2024-12-03)
	 * 
	 * @param $params {stdClass|arrayAssociative} — The parameters object.
	 * @param $params->data {object|array} — New item data. Existing item will be extended by this data.
	 * @param $params->data->{$propName} {mixed} — Keys are property names, values are values.
	 * @param [$params->where=''] {string|null} — SQL 'WHERE' clause. null or '' means that first found item will be updated.
	 * @param [$params->isEnabledAddNotFound=false] {boolean} — Is allowed to add item if it is not exist?
	 * 
	 * @return $result {stdClass|null} — An added item object or `null` if fail.
	 * @return $result->id {integer} — ID of added item.
	 */
	public function items_updateOne($params): ?\stdClass {
		$params = \DDTools\Tools\Objects::extend([
			'objects' => [
				// Defaults
				(object) [
					'isEnabledAddNotFound' => false,
					'data' => [],
				],
				$params,
			],
		]);
		
		$result = $this->items_update(
			\DDTools\Tools\Objects::extend([
				'objects' => [
					$params,
					[
						'limit' => 1,
					],
				],
			])
		);
		
		if (!empty($result)){
			$result = $result[0];
		}else{
			if ($params->isEnabledAddNotFound){
				$result = $this->items_addOne([
					'data' => $params->data,
				]);
			}else{
				$result = null;
			}
		}
		
		return $result;
	}
	
	/**
	 * items_delete
	 * @version 1.3 (2024-08-06)
	 * 
	 * @param [$params] {stdClass|arrayAssociative} — The parameters object. Default: —.
	 * @param [$params->where=''] {stdClass|arrayAssociative|string|null} — SQL 'WHERE' clause. null or '' means that all items will be deleted.
	 * @param $params->where->{$propName} {string|arrayIndexed} — Key is an item property name, value is a value. Only valid property names will be used, others will be ignored. You can specify multiple value variants through an array (SQL IN() operator), please note that empty arrays will just be ignored.
	 * @param $params->where->{$propName}[$i] {string} — A value.
	 * @param [$params->limit=0] {integer|0} — Maximum number of items to delete. `0` means all matching.
	 * @param [$params->offset=0] {integer} — Offset of the first item (can be useful with $params->limit).
	 * @param [$params->orderBy=''] {string} — SQL 'ORDER BY' clause (can be useful with $params->limit).
	 * 
	 * @return {void}
	 */
	abstract public function items_delete($params = []): void;
	
	/**
	 * items_deleteOne
	 * @version 1.3.1 (2024-12-03)
	 * 
	 * @param [$params] {stdClass|arrayAssociative} — The parameters object. Default: —.
	 * @param [$params->where=''] {stdClass|arrayAssociative|string|null} — SQL 'WHERE' clause. null or '' means that first found item will be deleted.
	 * @param $params->where->{$propName} {string|arrayIndexed} — Key is an item property name, value is a value. Only valid property names will be used, others will be ignored. You can specify multiple value variants through an array (SQL IN() operator), please note that empty arrays will just be ignored.
	 * @param $params->where->{$propName}[$i] {string} — A value.
	 * @param [$params->orderBy=''] {string} — SQL 'ORDER BY' clause.
	 * 
	 * @return {void}
	 */
	public function items_deleteOne($params = []): void {
		$this->items_delete(
			\DDTools\Tools\Objects::extend([
				'objects' => [
					$params,
					[
						'limit' => 1,
					],
				],
			])
		);
	}
	
	/**
	 * items_get
	 * @version 1.3 (2024-08-06)
	 * 
	 * @param [$params] {stdClass|arrayAssociative} — The parameters object.
	 * @param [$params->where=''] {stdClass|arrayAssociative|string|null} — SQL 'WHERE' clause. null or '' means that all items will be returned.
	 * @param $params->where->{$propName} {string|arrayIndexed} — Key is an item property name, value is a value. Only valid property names will be used, others will be ignored. You can specify multiple value variants through an array (SQL IN() operator), please note that empty arrays will just be ignored.
	 * @param $params->where->{$propName}[$i] {string} — A value.
	 * @param [$params->orderBy=''] {string} — SQL 'ORDER BY' clause.
	 * @param [$params->limit=0] {integer|0} — Maximum number of items to return. `0` means all matching.
	 * @param [$params->offset=0] {integer} — Offset of the first item (can be useful with $params->limit).
	 * @param [$params->propsToReturn='*'] {array|'*'|stringCommaSeparated} — Required item prop names to return. Can be set as array of prop names, comma separated string or '*' for all props.
	 * @param $params->propsToReturn[$i] {string} — A prop name.
	 * @param [$params->propAsResultKey=null] {string|null} — Item property, which value will be an item key in result array instead of an item index. For example, it can be useful if items have an ID property or something like that. `null` — result array will be indexed.
	 * @param [$params->propAsResultValue=null] {string|null} — Item property, which value will be an item value in result array instead of an item object.
	 * 
	 * @return $result {arrayIndexed|arrayAssociative} — An array of items. Item property values will be used as result keys if `$params->propAsResultKey` is set.
	 * @return $result[$itemIndex|$itemFieldValue] {stdClass|mixed} — A item object or item property value if specified in `$params->propAsResultValue`.
	 */
	abstract public function items_get($params = []): array;
	
	/**
	 * items_getOne
	 * @version 1.1.2 (2024-12-03)
	 * 
	 * @param [$params] {stdClass|arrayAssociative} — The parameters object.
	 * @param [$params->where=''] {string|null} — SQL 'WHERE' clause. null or '' means that first found item will be returned.
	 * @param [$params->orderBy=''] {string} — SQL 'ORDER BY' clause.
	 * @param [$params->propsToReturn='*'] {array|'*'|stringCommaSeparated} — Required item prop names to return. Can be set as array of prop names, comma separated string or '*' for all props.
	 * @param $params->propsToReturn[$i] {string} — A prop name.
	 * @param [$params->notFoundResult=null] {mixed} — What will be returned when no items found.
	 * 
	 * @return {stdClass|mixed} — Found item object or $params->notFoundResult.
	 */
	public function items_getOne($params = []){
		$params = \DDTools\Tools\Objects::extend([
			'objects' => [
				// Defaults
				(object) [
					'where' => '',
					'orderBy' => '',
					'propsToReturn' => '*',
					'notFoundResult' => null,
				],
				$params,
			],
		]);
		
		$result = $this->items_get([
			'where' => $params->where,
			'orderBy' => $params->orderBy,
			'propsToReturn' => $params->propsToReturn,
			'limit' => 1,
		]);
		
		if (!empty($result)){
			$result = $result[0];
		}else{
			$result = $params->notFoundResult;
		}
		
		return $result;
	}
	
	/**
	 * items_validateData
	 * @version 1.0.1 (2024-08-04)
	 * 
	 * @desc Returns only used properties (columns) of $params->data.
	 * 
	 * @param $params {stdClass|arrayAssociative} — The parameters object.
 	 * @param $params->data {stdClass|arrayAssociative} — An array/object of item properties (e. g. you can use $_POST). Properties with only valid names will be returned, others will be deleted.
 	 * @param $params->data->{$fieldName} {mixed} — Key is an item property name, value is a value.
	 * 
	 * @return {stdClass}
	 */
	abstract protected function items_validateData($params = []) :\stdClass;
	
	/**
	 * items_prepareWhere
	 * @version 1.2 (2024-08-06)
	 * 
	 * @desc Builds a where clause in the required internal format from externally passed parameters.
	 * 
	 * @param [$params] {stdClass|arrayAssociative} — The parameters object.
	 * @param [$params->where=''] {stdClass|arrayAssociative|string|null} — Data for SQL where. null or '' means that it is not used at all.
	 * @param $params->where->{$propName} {string|arrayIndexed} — Key is an item property name, value is a value. Only valid property names will be used, others will be ignored. You can specify multiple value variants through an array (SQL IN() operator), please note that empty arrays will just be ignored.
	 * @param $params->where->{$propName}[$i] {string} — A value.
	 * 
	 * @return {string}
	 */
	abstract protected function items_prepareWhere($params = []): string;
	
	/**
	 * escapeItemPropValue
	 * @version 1.0.1 (2024-08-04)
	 * 
	 * @param $params {stdClass|arrayAssociative} — The parameters object.
	 * @param $params->propName {string} — Name of item property.
	 * @param $params->propValue {string} — Value of item property.
	 * 
	 * @return {string}
	 */
	abstract protected function escapeItemPropValue($params): string;
}
?>