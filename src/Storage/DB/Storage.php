<?php
namespace DDTools\Storage\DB;

class Storage extends \DDTools\Storage\Storage {
	protected static
		/**
		 * @property $columnsDefaultParams {stdClass} — Default parameters for all columns. If some items are not defined in child classes, parent values will be used (see static::initStatic).
		 * @property $columnsDefaultParams->{$paramName} {mixed} — Key is a property name, value is a default value that will be used if the property is undefined.
		 */
		$columnsDefaultParams = [
			'attrs' => 'VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL',
			'isReadOnly' => false,
			'isPublic' => false,
			'isComparedCaseSensitive' => false,
			'isTagsAllowed' => false,
		]
	;
	
	protected
		/**
		 * @property $nameAlias {string} — Short table name (e. g. 'web_users'), full table name will be built from it. Must be defined in child classes or passed to constructor.
		 * @property $nameFull {string} — Full table name. Will be built automatically from $this->nameAlias.
		 */
		$nameAlias = '',
		$nameFull = '',
		
		/**
		 * @property $columns {\DDTools\ObjectCollection} — Table columns.
		 * @property $columns->items[$i] {stdClass} — Column data.
		 * @property $columns->items[$i]->name {string} — Column name.
		 * @property $columns->items[$i]->attrs {string} — Column attributes (empty value means static::$columnsDefaultParams->attrs). Default: —.
		 * @property $columns->items[$i]->isReadOnly {boolean} — Can column be modified? Default: false.
		 * @property $columns->items[$i]->isPublic {boolean} — Can column be used quite safely? Default: false.
		 * @property $columns->items[$i]->isComparedCaseSensitive {boolean} — Should column to be compared case-sensitive in where clauses? Default: false.
		 * @property $columns->items[$i]->isTagsAllowed {boolean} — Are HTML and MODX tags allowed? Default: false.
		 */
		$columns = [
			[
				'name' => 'id',
				'attrs' => 'INTEGER(10) AUTO_INCREMENT PRIMARY KEY',
				'isReadOnly' => true,
			],
		]
	;
	
	/**
	 * initStatic
	 * @version 1.1.1 (2023-12-29)
	 * 
	 * @desc Static “constructor”.
	 * 
	 * @return {void}
	 */
	private static function initStatic(): void {
		//If is not inited before (type of static::$columnsDefaultParams is just used as flag)
		if (!is_object(static::$columnsDefaultParams)){
			//Merge columnsDefaultParams from parent and child static props
			static::$columnsDefaultParams = \DDTools\ObjectTools::extend([
				'objects' => [
					//Parent (\DDTools\DB\Table::$columnsDefaultParams)
					(object) self::$columnsDefaultParams,
					//Child (e.g. \Something\Table\Base\Table::$columnsDefaultParams)
					static::$columnsDefaultParams
				]
			]);
		}
	}
	
	/**
	 * __construct
	 * @version 3.0 (2024-01-04)
	 * 
	 * @param $params {arrayAssociative|stdClass} — Parameters, the pass-by-name style is used. Default: —.
	 * @param $params->nameAlias {string} — Short table name (e. g. 'site_content'). You can define it in child classes or pass to the constructor directly. Default: ''.
	 * @param $params->columns {array} — Additional columns (that not defined in the class). Default: [].
	 * @param $params->columns[$i] {stdClass|arrayAssociative|string} — Column parameters. Can be set as a simple string name if other parameters should be set by default.
	 * @param $params->columns[$i]->name {string} — Column name. @required
	 * @param $params->columns[$i]->isPublic {boolean} — Can column be used quite safely? Default: true.
	 * @param $params->columns[$i]->attrs {string} — Column attributes (empty value means static::$columnsDefaultParams->attrs). Default: static::$columnsDefaultParams->attrs.
	 * @param $params->columns[$i]->isReadOnly {boolean} — Can column be modified? Default: static::$columnsDefaultParams->isReadOnly.
	 * @param $params->columns[$i]->isComparedCaseSensitive {boolean} — Should column to be compared case-sensitive in where clauses? Default: static::$columnsDefaultParams->isComparedCaseSensitive.
	 * @param $params->columns[$i]->isTagsAllowed {boolean} — Are HTML and MODX tags allowed? Default: static::$columnsDefaultParams->isTagsAllowed.
	 */
	public function __construct($params = []){
		$params = \DDTools\ObjectTools::extend([
			'objects' => [
				(object) [
					'columns' => []
				],
				$params
			]
		]);
		
		//Init static
		static::initStatic();
		
		$this->construct_props($params);
		
		$this->construct_db();
	}
	
	/**
	 * construct_props
	 * @version 2.0 (2024-01-04)
	 * 
	 * @param $params {stdClass} — Parameters, see $this->__construct. @required
	 * 
	 * @return {void}
	 */
	private function construct_props($params): void {
		if (!empty($params->nameAlias)){
			$this->nameAlias = $params->nameAlias;
		}
		
		//Prepare table name
		$this->nameFull = \ddTools::$modx->getFullTableName($this->nameAlias);
		
		$this->columns = new \DDTools\ObjectCollection([
			'items' => $this->columns,
			'itemType' => 'objectStdClass',
		]);
		
		if (!empty($params->columns)){
			//Save additional columns to others
			foreach (
				$params->columns as
				$columnParams
			){
				//If column is set as a simple string name
				if (is_string($columnParams)){
					$columnParams = [
						'name' => $columnParams
					];
				}
				
				$this->columns->addItems([
					'items' => [
						\DDTools\ObjectTools::extend([
							'objects' => [
								//Column data
								(object) [
									//All additional columns are considered as safe by default
									'isPublic' => true,
								],
								$columnParams,
							]
						])
					],
				]);
			}
		}
	}
	
	/**
	 * construct_db
	 * @version 1.0.1 (2024-01-04)
	 * 
	 * @return {void}
	 */
	private function construct_db(): void {
		//We can't do something without table name
		if (!empty($this->nameAlias)){
			$isTableExist = boolval(
				\ddTools::$modx->db->getValue(
					'show tables like "' .
					\ddTools::$modx->db->config['table_prefix'] .
					$this->nameAlias .
					'"'
				)
			);
			
			//By default, consider that columns are absent
			$columnsExisting = [];
			
			$columnsQuery = [];
			
			//If table exists
			if ($isTableExist){
				//Получаем существующие колонки
				$columnsExisting = \ddTools::$modx->db->getColumnNames(
					\ddTools::$modx->db->select(
						'*',
						$this->nameFull,
						//Что угодно, -1 выбран, так как таких записей точно быть не должно
						'`id` = -1'
					)
				);
			}
			
			foreach (
				$this->columns->getItems() as
				$columnData
			){
				foreach (
					static::$columnsDefaultParams as
					$propName =>
					$propDefaultValue
				){
					if (
						!\DDTools\ObjectTools::isPropExists([
							'object' => $columnData,
							'propName' => $propName
						])
					){
						$columnData->{$propName} = $propDefaultValue;
					}
				}
				
				//If the column is not exist
				if (
					!in_array(
						$columnData->name,
						$columnsExisting
					)
				){
					$columnsQuery[] =
						(
							$isTableExist ?
							'ADD ' :
							''
						) .
						$columnData->name .
						' ' .
						$columnData->attrs
					;
				}
			}
			
			if (!empty($columnsQuery)){
				$columnsQuery = implode(
					', ',
					$columnsQuery
				);
				
				//If table exists
				if ($isTableExist){
					//Create missing columns
					$resultQuery =
						'ALTER TABLE ' .
						$this->nameFull .
						' ' .
						$columnsQuery
					;
				}else{
					//Create table with needed columns
					$resultQuery =
						'CREATE TABLE IF NOT EXISTS ' .
						$this->nameFull .
						' (' .
						$columnsQuery .
						')'
					;
				}
				
				//Create the table or add/change columns
				\ddTools::$modx->db->query($resultQuery);
			}
		}
	}
	
	/**
	 * cols_getColsParams
	 * @version 3.0 (2023-12-28)
	 * 
	 * @param $params {arrayAssociative|stdClass} — Parameters, the pass-by-name style is used. Default: —.
	 * @param $params->paramName {'name'|'attrs'} — Column property to return. Default: 'name'.
	 * @param $params->filter {string} — Filter clause for column properties, see `\DDTools\ObjectCollection`. Default: ''.
	 * 
	 * @return $result {arrayAssociative}
	 * @return $result[$columnName] {string} — Key is column name, value is column property defined by $params->paramName.
	 */
	protected function cols_getColsParams($params = []): array {
		$params = \DDTools\ObjectTools::extend([
			'objects' => [
				//Defaults
				(object) [
					'paramName' => 'name',
					'filter' => '',
				],
				$params
			]
		]);
		
		return $this->columns->getItems([
			'propAsResultKey' => 'name',
			'propAsResultValue' => $params->paramName,
			'filter' => $params->filter,
		]);
	}
	
	/**
	 * cols_getOneColParam
	 * @version 2.0 (2023-12-28)
	 * 
	 * @param $params {arrayAssociative|stdClass} — Parameters, the pass-by-name style is used. Default: —.
	 * @param $params->filter {string} — Filter clause for column properties, see `\DDTools\ObjectCollection`. Default: ''.
	 * @param $params->paramName {'name'|'attrs'} — Column property to return. Default: 'name'.
	 * 
	 * @return {mixed|null} — `null` means that column or property is not exist.
	 */
	protected function cols_getOneColParam($params = []){
		$params = \DDTools\ObjectTools::extend([
			'objects' => [
				//Defaults
				(object) [
					'filter' => '',
					'paramName' => 'name',
				],
				$params
			]
		]);
		
		return \DDTools\ObjectTools::getPropValue([
			'object' => $this->columns->getOneItem([
				'filter' => $params->filter,
			]),
			'paramName' => $params->paramName
		]);
	}
	
	/**
	 * cols_getValidNames
	 * @version 1.1.2 (2023-12-28)
	 * 
	 * @desc Gets valid column names.
	 * 
	 * @param $params {stdClass|arrayAssociative} — The object of parameters. @required
	 * @param $params->colNames {array|'*'|stringCommaSeparated} — Required column names. Can be set as array of column names, comma separated string or '*' for all columns. Only valid column names will be returned. Default: '*' (all).
	 * @param $params->colNames[$i] {string} — A column name. @required
	 * 
	 * @return $result {arrayIndexed}
	 * @return $result[$i] {string}
	 */
	protected function cols_getValidNames($params = []): array {
		$params = \DDTools\ObjectTools::extend([
			'objects' => [
				(object) [
					'colNames' => '*'
				],
				$params
			]
		]);
		
		//Return all exist columns by default
		$result = array_values($this->cols_getColsParams());
		
		//If we don't need all esixt columns
		if (
			$params->colNames != '*' &&
			!empty($params->colNames)
		){
			//If column names are set as single column name
			if (!is_array($params->colNames)){
				$params->colNames = explode(
					',',
					$params->colNames
				);
			}
			
			//Delete non-existent columns
			$result = array_intersect(
				$params->colNames,
				$result
			);
		}
		
		return $result;
	}
	
	/**
	 * items_add
	 * @version 1.2 (2023-12-28)
	 * 
	 * @param $params {stdClass|arrayAssociative} — The object of parameters. @required
	 * @param $params->items {mixed} — An array of items. @required
	 * 	{array} — can be indexed or associative, keys will not be used
	 * 	{object} — also can be set as an object for better convenience, only property values will be used
	 * 	{stringJsonObject} — [JSON](https://en.wikipedia.org/wiki/JSON) object
	 * 	{stringJsonArray} — [JSON](https://en.wikipedia.org/wiki/JSON) array
	 * 	{stringHjsonObject} — [HJSON](https://hjson.github.io/) object
	 * 	{stringHjsonArray} — [HJSON](https://hjson.github.io/) array
	 * 	{stringQueryFormatted} — [Query string](https://en.wikipedia.org/wiki/Query_string)
	 * @param $params->items[$itemIndex] {object|array} — An item. @required
	 * @param $params->items[$itemIndex]->{$propName} {mixed} — Keys are property names, values are values. @required
	 * 
	 * @return $result {arrayIndexed} — Array of added items.
	 * @return $result[$itemIndex] {stdClass} — A item object.
	 * @return $result[$itemIndex]->id {integer} — ID of added item.
	 */
	public function items_add($params): array {
		$params = (object) $params;
		
		//Items must be an array
		if (!is_array($params->items)){
			$params->items = \DDTools\ObjectTools::convertType([
				'object' => $params->items,
				'type' => 'objectArray'
			]);
		}
		
		$result = [];
		
		foreach (
			$params->items as
			$itemObject
		){
			$itemObject = (object) $itemObject;
			
			//ID can't be inserted
			unset($itemObject->id);
			
			$itemObject = $this->items_validateData([
				'data' => $itemObject
			]);
			
			\ddTools::$modx->db->query('
				INSERT INTO
					' . $this->nameFull . '
				SET
					' . $this->buildSqlSetString(['data' => $itemObject]) . '
			');
			
			$itemObject->id = \ddTools::$modx->db->getInsertId();
			
			if ($itemObject->id !== false){
				$result[] = $itemObject;
			}
		}
		
		return $result;
	}
	
	/**
	 * items_update
	 * @version 1.3.2 (2023-12-29)
	 * 
	 * @param $params {stdClass|arrayAssociative} — The object of parameters. @required
	 * @param $params->data {object|array} — New item data. Existing item will be extended by this data. @required
	 * @param $params->data->{$propName} {mixed} — Keys are property names, values are values. @required
	 * @param $params->where {stdClass|arrayAssociative|string} — SQL 'WHERE' clause. Default: '' (all items will be updated).
	 * @param $params->where->{$fieldName} {string} — Key is a property name, value is a value. Only valid properties names will be used, others will be ignored. @required
	 * @param $params->limit {integer|0} — Maximum number of items to delete. `0` means all matching. Default: 0.
	 * @param $params->offset {integer} — Offset of the first item (can be useful with $params->limit). Default: 0.
	 * 
	 * @return $result {arrayIndexed} — Array of updated items.
	 * @return $result[$itemIndex] {stdClass} — A item object.
	 * @return $result[$itemIndex]->id {integer} — ID of added item.
	 */
	public function items_update($params): array {
		$params = \DDTools\ObjectTools::extend([
			'objects' => [
				//Defaults
				(object) [
					'where' => '',
					'data' => [],
				],
				$params
			]
		]);
		
		$result = [];
		
		$params->data = $this->items_validateData([
			'data' => $params->data
		]);
		
		//Validate data (keep all except unsaveable)
		$params->data = array_diff_key(
			(array) $params->data,
			//ReadOnly props can't be updated
			$this->cols_getColsParams([
				'filter' => 'isReadOnly == 1'
			])
		);
		
		//Collect all updated resource IDs to a SQL variable
		\ddTools::$modx->db->query('SET @updated_ids := ""');
		\ddTools::$modx->db->query('
			UPDATE
				' . $this->nameFull . '
			SET
				' . $this->buildSqlSetString(['data' => $params->data]) . '
			WHERE
				(
					' . $this->buildSqlWhereString($params) . '
				)
				AND (
					@updated_ids := IF (
						@updated_ids = "",
						`id`,
						CONCAT_WS(
							",",
							`id`,
							@updated_ids
						)
					)
				)
			' . static::buildSqlLimitString($params) . '
		');
		$dbResult = \ddTools::$modx->db->getValue(
			\ddTools::$modx->db->query('
				SELECT @updated_ids
			')
		);
		
		//Comma separated string or fail
		if (
			is_string($dbResult) &&
			!empty($dbResult)
		){
			$dbResult = explode(
				',',
				$dbResult
			);
			
			foreach (
				$dbResult as
				$itemId
			){
				$result[] = \DDTools\ObjectTools::extend([
					'objects' => [
						(object) [
							'id' => $itemId
						],
						$params->data
					]
				]);
			}
		}
		
		return $result;
	}
	
	/**
	 * items_delete
	 * @version 1.1 (2023-12-26)
	 * 
	 * @param $params {stdClass|arrayAssociative} — The object of parameters. Default: —.
	 * @param $params->where {stdClass|arrayAssociative|string} — SQL 'WHERE' clause. Default: '' (all items will be deleted).
	 * @param $params->where->{$fieldName} {string} — Key is a property name, value is a value. Only valid properties names will be used, others will be ignored. @required
	 * @param $params->limit {integer|0} — Maximum number of items to delete. `0` means all matching. Default: 0.
	 * @param $params->offset {integer} — Offset of the first item (can be useful with $params->limit). Default: 0.
	 * @param $params->orderBy {string} — SQL 'ORDER BY' clause (can be useful with $params->limit). Default: ''.
	 * 
	 * @return {void}
	 */
	public function items_delete($params = []): void {
		$params = \DDTools\ObjectTools::extend([
			'objects' => [
				//Defaults
				(object) [
					'where' => '',
					'orderBy' => '',
				],
				$params
			]
		]);
		
		\ddTools::$modx->db->delete(
			//Table
			$this->nameFull,
			//Where
			$this->buildSqlWhereString($params),
			//OrderBy
			$params->orderBy,
			//Limit
			static::buildSqlLimitString($params)
		);
	}
	
	/**
	 * items_get
	 * @version 1.2 (2023-12-26)
	 * 
	 * @param $params {stdClass|arrayAssociative} — The object of parameters. Default: —.
	 * @param $params->where {stdClass|arrayAssociative|string} — SQL 'WHERE' clause. Default: '' (all items will be returned).
	 * @param $params->where->{$fieldName} {string} — Key is a property name, value is a value. Only valid properties names will be used, others will be ignored. @required
	 * @param $params->orderBy {string} — SQL 'ORDER BY' clause. Default: ''.
	 * @param $params->limit {integer|0} — Maximum number of items to return. `0` means all matching. Default: 0.
	 * @param $params->offset {integer} — Offset of the first item (can be useful with $params->limit). Default: 0.
	 * @param $params->propsToReturn {array|'*'|stringCommaSeparated} — Required item prop names to return. Can be set as array of prop names, comma separated string or '*' for all props. Default: '*' (all).
	 * @param $params->propsToReturn[$i] {string} — A prop name. @required
	 * @param $params->propAsResultKey {string|null} — Item property, which value will be an item key in result array instead of an item index. For example, it can be useful if items have an ID property or something like that. `null` — result array will be indexed. Default: null.
	 * @param $params->propAsResultValue {string|null} — Item property, which value will be an item value in result array instead of an item object. Default: null.
	 * 
	 * @return $result {arrayIndexed|arrayAssociative} — An array of items. Item property values will be used as result keys if `$params->propAsResultKey` is set.
	 * @return $result[$itemIndex|$itemFieldValue] {stdClass|mixed} — A item object or item property value if specified in `$params->propAsResultValue`.
	 */
	public function items_get($params = []): array {
		$params = \DDTools\ObjectTools::extend([
			'objects' => [
				//Defaults
				(object) [
					'where' => '',
					'orderBy' => '',
					'propsToReturn' => '*',
					'propAsResultKey' => null,
					'propAsResultValue' => null,
				],
				$params
			]
		]);
		
		$result = [];
		
		$params->propsToReturn = $this->cols_getValidNames([
			'colNames' => $params->propsToReturn
		]);
		
		if (!empty($params->propsToReturn)){
			$sqlResult = \ddTools::$modx->db->select(
				//Fields
				implode(
					',',
					$params->propsToReturn
				),
				//Table
				$this->nameFull,
				//Where
				$this->buildSqlWhereString($params),
				//Order by
				$params->orderBy,
				//Limit
				$this->buildSqlLimitString($params)
			);
			
			if ($sqlResult){
				while ($itemData = \ddTools::$modx->db->getRow($sqlResult)){
					$itemData = (object) $itemData;
					
					//Save only field value instead of object if needed
					if (!is_null($params->propAsResultValue)){
						$resultItemObject = \DDTools\ObjectTools::getPropValue([
							'object' => $itemData,
							'propName' => $params->propAsResultValue
						]);
					}else{
						$resultItemObject = $itemData;
					}
					
					//Save item
					if (!is_null($params->propAsResultKey)){
						$result[
							\DDTools\ObjectTools::getPropValue([
								'object' => $itemData,
								'propName' => $params->propAsResultKey
							])
						] = $resultItemObject;
					}else{
						$result[] = $resultItemObject;
					}
				}
			}
		}
		
		return $result;
	}
	
	/**
	 * items_validateData
	 * @version 1.0.1 (2023-12-28)
	 * 
	 * @desc Returns only used properties (columns) of $params->data.
	 * 
	 * @param $params {stdClass|arrayAssociative} — The object of parameters. @required
 	 * @param $params->data {stdClass|arrayAssociative} — An array/object of item properties (e. g. you can use $_POST). Properties with only valid names will be returned, others will be deleted. @required
 	 * @param $params->data->{$fieldName} {mixed} — Key is an item property name, value is a value. @required
	 * 
	 * @return {stdClass}
	 */
	protected function items_validateData($params = []) :\stdClass {
		$params = (object) $params;
		
		$params->data = (array) $params->data;
		
		//Filter data (keep only used field names)
		$params->data = array_intersect_key(
			$params->data,
			array_fill_keys(
				//Only used field names
				$this->cols_getValidNames([
					'colNames' => array_keys($params->data)
				]),
				//No matter because the only keys will be used for comparison
				null
			)
		);
		
		return (object) $params->data;
	}
	
	/**
	 * buildSqlWhereString
	 * @version 1.1.2 (2023-12-28)
	 * 
	 * @desc Builds where clause string from array.
	 * 
	 * @param $params {stdClass|arrayAssociative} — The object of parameters.
	 * @param $params->where {stdClass|arrayAssociative|string} — Data for SQL where. Default: ''.
	 * @param $params->where->{$propName} {string} — Key is an item property name, value is a value. Only valid property names will be used, others will be ignored. @required
	 * 
	 * @return {string}
	 */
	final protected function buildSqlWhereString($params = []): string {
		$params = \DDTools\ObjectTools::extend([
			'objects' => [
				(object) [
					'where' => ''
				],
				$params
			]
		]);
		
		if (is_string($params->where)){
			$result = $params->where;
		//If it is array or object
		}else{
			//Validate where conditions (keep only used field names)
			$params->where = $this->items_validateData([
				'data' => $params->where
			]);
			
			$result = [];
			
			foreach (
				$params->where as
				$propName =>
				$propValue
			){
				$result[] =
					$propName . ' = ' .
					//Case-sensitive comparison or not?
					(
						$this->cols_getOneColParam([
							'filter' => 'name == ' . $propName,
							'paramName' => 'isComparedCaseSensitive',
						]) ?
						'BINARY ' :
						''
					) .
					'"' . $this->escapeItemPropValue([
						'propName' => $propName,
						'propValue' => $propValue,
					]) . '"'
				;
			}
			
			$result = implode(
				' AND ',
				$result
			);
		}
		
		return $result;
	}
	
	/**
	 * escapeItemPropValue
	 * @version 2.0.2 (2023-12-28)
	 * 
	 * @param $params {stdClass|arrayAssociative} — The object of parameters. @required
	 * @param $params->propName {string} — Name of item property. @required
	 * @param $params->propValue {string} — Value of item property. @required
	 * 
	 * @return {string}
	 */
	final protected function escapeItemPropValue($params): string {
		$params = (object) $params;
		
		//Strip tags if required
		if (
			!$this->cols_getColsParams([
				'filter' => 'name == ' . $params->propName,
				'paramName' => 'isTagsAllowed',
			])
		){
			$params->propValue = \ddTools::$modx->stripTags($params->propValue);
		}
		
		return \ddTools::$modx->db->escape($params->propValue);
	}
	
	/**
	 * buildSqlSetString
	 * @version 1.0.2 (2023-12-26)
	 * 
	 * @param $params {stdClass|arrayAssociative} — The object of parameters. @required
	 * @param $params->data {object|array} — Item data. @required
	 * @param $params->data->{$propName} {mixed} — Keys are property names, values are values. @required
	 * 
	 * @return {string}
	 */
	final protected function buildSqlSetString($params): string {
		$params = (object) $params;
		
		$result = [];
		
		foreach (
			$params->data as
			$propName =>
			$propValue
		){
			$result[] = '`' . $propName . '` = "' . $this->escapeItemPropValue([
				'propName' => $propName,
				'propValue' => $propValue,
			]) . '"';
		}
		
		$result = implode(
			',',
			$result
		);
		
		return $result;
	}
	
	/**
	 * buildSqlLimitString
	 * @version 1.0 (2023-12-08)
	 * 
	 * @param $params {stdClass|arrayAssociative} — The object of parameters. Default: —.
	 * @param $params->limit {integer|0} — Maximum number of items to return. `0` means all matching. Default: 0.
	 * @param $params->offset {integer} — Offset of the first item. Default: 0.
	 * 
	 * @return {string}
	 */
	final protected static function buildSqlLimitString($params = []): string {
		$params = \DDTools\ObjectTools::extend([
			'objects' => [
				//Defaults
				(object) [
					'limit' => 0,
					'offset' => 0,
				],
				$params
			]
		]);
		
		return
			//If limit is used
			$params->limit > 0 ?
			(
				'LIMIT ' .
				//Offset
				(
					$params->offset > 0 ?
					$params->offset . ', ' :
					''
				) .
				//Count
				$params->limit
			) :
			//Without limit
			''
		;
	}
}
?>