<?php
	namespace sys;

	class PDO extends \PDO{
		public static function prepareIn($key, $values, &$param = array()){
			$keys = array();

			$param = (array)$param;
			for($i = 0; $i < count($values); $i++){
				$i_key = $key.'_'.$i;
				$keys[] = $i_key;
				$param[$i_key] = $values[$i];
			}

			$in_clause = implode(', ', $keys);

			return (empty($in_clause))? 'NULL' : $in_clause;
		}

		public function getJsDate($field, $withOutSec = false){
			switch($this->getAttribute(static::ATTR_DRIVER_NAME)){
				case 'pgsql':
					$format = (!$withOutSec)? 'YYYY-MM-DD"T"HH24:MI:SS"Z"' : 'YYYY-MM-DD"T"HH24:MI"Z"';
					return "to_char({$field}::timestamp with time zone at time zone 'Z', '{$format}')";
				case 'mysql':
					$format = (!$withOutSec)? '%Y-%m-%dT%TZ' : '%Y-%m-%dT%H:%iZ';
					return "DATE_FORMAT({$field}, '".$format."')";
				default:
					throw new \Exception('Unsupported PDO driver!!!');
			}
		}

		public function getDbDate($value){
			switch($this->getAttribute(static::ATTR_DRIVER_NAME)){
				case 'pgsql':
					return $value;
				case 'mysql':
					$values = explode('T', $value);
					$values[1] = preg_replace("/Z$/i", '', $values[1]);
					return "{$values[0]} {$values[1]}";
				default:
					throw new \Exception('Unsupported PDO driver!!!');
			}
		}

		public function getCurrentTimestamp($forjs = false, $withOutSec = false){
			$stmt = $this->prepare("SELECT ".(($forjs)? $this->getJsDate('CURRENT_TIMESTAMP', $withOutSec) : 'CURRENT_TIMESTAMP'));
			$stmt->execute();
			return $stmt->fetch(\PDO::FETCH_COLUMN);
		}
	}

	class PDOConfigurated{
		private $conf = null;
		private $pdo = null;

		function __construct($conf){
			$this->conf = $conf;
		}

		public function getInstance(){
			if($this->pdo === null){
				$this->pdo = new PDO(
					  $this->conf['dns']
					, $this->conf['username']
					, $this->conf['password']
					, (!empty($this->conf['options']))? $this->conf['options'] : null
				);
			}

			return $this->pdo;
		}
	}
?>
