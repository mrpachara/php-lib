<?php
	namespace sys;

	class DataService {
		const ANNO_INITSQLSTMT_MAP = [];
		const ANNO_EXISTEDMETHOD_MAP = [];

		const SEARCH_DELIMITER = "_:::_";

		public static function addDash($value){
			return preg_replace_callback('/[A-Z]/', function($c){
				return '-'.$c[0];
			}, $value);
		}

		protected static function safeUpdate($pdo, $stmts, $criteria, $deleteSql, $datas, $fields = null){
			$stmts['existed']->execute($criteria);
			$existedIds = $stmts['existed']->fetchAll(\PDO::FETCH_COLUMN);
			$stmts['existed']->closeCursor();

			/* prepare insertData and updateData */
			$insertData = [];
			$updateData = [];
			$keepIds = [];
			foreach($datas as $data){
				if(!empty($data['id']) && in_array($data['id'], $existedIds)){
					$updateData[] = $data;
					$keepIds[] = $data['id'];
				} else{
					$insertData[] = $data;
				}
			}

			/* delete existed data */
			$inParams = [];
			$undeleteSql = "AND (id NOT IN (".\sys\PDO::prepareIn(':_undeleted_', $keepIds, $inParams)."))";
			$stmt = $pdo->prepare(sprintf($deleteSql, (empty($inParams))? "" : $undeleteSql));
			$stmt->execute(array_merge($criteria, $inParams));
			$stmt->closeCursor();

			$fields_update = null;
			$fields_insert = null;
			if(is_array($fields)){
				if(array_key_exists('update', $fields)){
					$fields_update = $fields['update'];
				}
				if(array_key_exists('insert', $fields)){
					$fields_insert = $fields['insert'];
				}
			}

			/* update data before insert to prevent conflict constrain */
			$driver = $pdo->getAttribute(\PDO::ATTR_DRIVER_NAME);
			$tryUpdateData = $updateData;
			$lastLength = count($tryUpdateData) + 1;
			$lastExcp = null;
			while(count($tryUpdateData) > 0){
				if($lastLength == count($tryUpdateData)) throw $lastExcp;
				$nextUpdateData = [];
				$lastLength = count($tryUpdateData);
				foreach($tryUpdateData as $data){
					if($driver == 'pgsql') $pdo->query("SAVEPOINT tryupdate_child_data_if_avaliable");
					try{
						$stmts['update']->execute(($fields_update !== null)? array_intersect_key($data, array_flip($fields_update)) : $data);
					} catch(\PDOException $excp){
						$lastExcp = $excp;
						$nextUpdateData[] = $data;
						if($driver == 'pgsql') $pdo->query("ROLLBACK TO SAVEPOINT tryupdate_child_data_if_avaliable");
					}
					if($driver == 'pgsql') $pdo->query("RELEASE SAVEPOINT tryupdate_child_data_if_avaliable");
					$stmts['update']->closeCursor();
					$tryUpdateData = $nextUpdateData;
				}
			}

			/* insert data */
			foreach($insertData as $data){
				$stmts['insert']->execute(($fields_insert !== null)? array_intersect_key($data, array_flip($fields_insert)) : $data);
				$stmts['insert']->closeCursor();
			}
		}

		public static function isPrefix($prefix, $value){
			$prefix = static::addDash($prefix).'-';
			$value = static::addDash($value).'-';

			return (strpos($value, $prefix) === 0);
		}

		protected function createSearchTerm($termText){
			$searchTerm = array(
				  'terms' => array()
				, 'specials' => array()
			);

			if(empty($termText)) return $searchTerm;

			foreach(explode(' ', $termText) as $term){
				$termSplited = explode(':', $term, 2);

				if(count($termSplited) == 1){
					$searchTerm['terms'][] = $term;
				} else{
					if(empty($searchTerm['specials'][$termSplited[0]])) $searchTerm['specials'][$termSplited[0]] = array();

					$searchTerm['specials'][$termSplited[0]][] = $termSplited[1];
				}
			}

			return $searchTerm;
		}

		protected function initSqlStatement(){
			$sqlStatement = array(
				  'sqls' => array()
				, 'params' => array()
				, 'forupdate' => ''
				, 'limit' => ''
			);

			return $sqlStatement;
		}

		protected function extendWhereSearchSpecial($searchTerm, &$existedSqlStatement = null){
			$where = array(
				  'sqls' => array()
				, 'params' => array()
			);

			$existedSqlStatement = array_merge_recursive((array)$existedSqlStatement, $where);

			return $where;
		}

		protected function extendWhereSearchTerm($searchTerm, $searchableFields, &$existedSqlStatement = null){
			$where = array(
				  'sqls' => array()
				, 'params' => array()
			);

			if(empty($searchableFields) || empty($searchTerm['terms'])) return $where;

			$concatFn = "concat_ws('".static::SEARCH_DELIMITER."', ".implode(', ', $searchableFields).")";

			for($i = 0; $i < count($searchTerm['terms']); $i++){
				$term = $searchTerm['terms'][$i];
				$paramName = ":_term_".$i;
				switch($this->getPdo()->getAttribute(\PDO::ATTR_DRIVER_NAME)){
					case 'pgsql':
						$where['sqls'][] = "({$concatFn} ILIKE {$paramName})";
						break;
					default:
						$where['sqls'][] = "({$concatFn} LIKE {$paramName})";
				}
				$where['params'][$paramName] = "%{$term}%";
			}

			$existedSqlStatement = array_merge_recursive((array)$existedSqlStatement, $where);

			return $where;
		}

		protected function extendWhereQuery($queries, $keyMap, &$existedSqlStatement = null){
			$where = array(
				  'sqls' => array()
				, 'params' => array()
			);

			$keyMap = (array)$keyMap;

			foreach((array)$queries as $key => $query){
				if(array_key_exists($key, $keyMap) && !empty($query)){
					$where['sqls'][] = "({$keyMap[$key]} IN (".PDO::prepareIn(":_query_{$key}", $query, $where['params'])."))";
				} else{
					$where = array(
						  'sqls' => array('FALSE')
						, 'params' => array()
					);
					break;
				}
			}

			$existedSqlStatement = array_merge_recursive((array)$existedSqlStatement, $where);

			return $where;
		}

		protected function extendLimit(&$page, &$existedSqlStatement = null){
			$limitSql = '';

			if(empty($page)) return $limitSql;

			$sql_page = (is_numeric($page))? (int)$page - 1 : 0;
			$sql_limit = $this->conf['pagination']['numofitem'];
			if(is_array($page)){
				if(array_key_exists('current', $page) && is_numeric($page['current'])) $sql_page = (int)$page['current'] - 1;
				if(array_key_exists('limit', $page) && is_numeric($page['limit'])) $sql_limit = (int)$page['limit'];
			}

			if($sql_page < 0) $sql_page = 0;
			$offset = $sql_page * $sql_limit;

			$limitSql = "LIMIT {$limit} OFFSET {$offset}";

			$page = array(
				  'current' => $sql_page + 1
				, 'previous' => ($sql_page > 0)? $sql_page : null
				, 'next' => $sql_page + 2
				, 'total' => null
				, 'limit' => $sql_limit
			);

			$existedSqlStatement = (array)$existedSqlStatement;
			$existedSqlStatement['limit'] = $limitSql;

			return $limitSql;
		}

		protected function prepareEntity(&$data){
			return;
		}

		protected function extendAction(&$data){
			if(empty($data)) return;

			$data['_updatable'] = true;
			$data['_deletable'] = true;
		}

		protected $pdoconfigurated = null;

		public function __construct($pdoconfigurated){
			$this->pdoconfigurated = $pdoconfigurated;
		}

		public function __call($method, $args){
			if(($method === 'getPdo') || (!in_array($method, get_class_methods($this)))) throw new \sys\DataServiceException("%s unknow {$method} method", 400);

			$initSqlStatement = 'initSqlStatement';
			if(array_key_exists($method, static::ANNO_INITSQLSTMT_MAP)){
				$initSqlStatement = static::ANNO_INITSQLSTMT_MAP[$method];
			}

			$sqlStatement = $this->$initSqlStatement();
			if(static::isPrefix('get', $method)){
				if(isset($args[1]) && is_array($args[1])){
					foreach($args[1] as $key => $value){
						if(array_key_exists($key, $sqlStatement)){
							if(is_array($value)){
								$sqlStatement[$key] = array_merge(
									(array)$sqlStatement[$key],
									$value
								);
							} else{
								$sqlStatement[$key] = $value;
							}
						} else{
							$sqlStatement[$key] = $value;
						}
					}
				}
				$data = $this->$method((isset($args[0]))? $args[0] : null, $sqlStatement);
				if(static::isPrefix('getAll', $method)){
					foreach($data as &$item){
						$this->prepareEntity($item);
						$this->extendAction($item);
					}
				} else{
					$this->prepareEntity($data);
					$this->extendAction($data);
				}

				return $data;
			} else if(static::isPrefix('load', $method)){
				$get = 'get';
				if(array_key_exists($method, static::ANNO_EXISTEDMETHOD_MAP)){
					$get = static::ANNO_EXISTEDMETHOD_MAP[$method];
				}
				$existedData = $this->__call($get, array(isset($args[0])? $args[0] : null, $sqlStatement));

				if($existedData === false){
					throw new \sys\DataServiceException("%s not found", 404);
				}

				array_shift($args);
				array_unshift($args, $existedData);

				return call_user_func_array(array($this, $method), $args);
			} else if(static::isPrefix('save', $method) || static::isPrefix('delete', $method) || static::isPrefix('update', $method)){
				$result = null;

				$this->getPdo()->beginTransaction();
				try{
					$sqlStatement['forupdate'] = 'FOR UPDATE';
					$get = 'get';
					if(array_key_exists($method, static::ANNO_EXISTEDMETHOD_MAP)){
						$get = static::ANNO_EXISTEDMETHOD_MAP[$method];
					}
					$existedData = $this->__call($get, array(isset($args[0])? $args[0] : null, $sqlStatement));

					if($existedData === false){
						throw new \sys\DataServiceException("%s not found", 404);
					}

					if((static::isPrefix('save', $method) || static::isPrefix('update', $method)) && !$existedData['_updatable']){
						throw new \sys\DataServiceException("%s cannot be updated", 400);
					}

					if(static::isPrefix('delete', $method) && !$existedData['_deletable']){
						throw new \sys\DataServiceException("%s cannot be deleted", 400);
					}

					array_shift($args);
					array_unshift($args, $existedData);

					//$result = (static::isPrefix('delete', $method))? $this->$method($existedData) : $this->$method($existedData, $args[1]);
					$result = call_user_func_array(array($this, $method), $args);
				} catch(\PDOException $excp){
					$this->getPdo()->rollBack();
					throw $excp;
				}
				return ($this->getPdo()->commit())? $result : false;
			} else{
				throw new \sys\DataServiceException("%s unknow {$method} method", 400);
			}
		}

		protected function getPdo(){
			return $this->pdoconfigurated->getInstance();
		}
	}
?>
