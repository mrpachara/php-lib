<?php
	namespace sys;

	class PDOConfigurated {
		private $conf = null;
		private $pdo = null;

		function __construct($conf){
			$this->$conf = $conf;
		}

		function getInstance(){
			if($pdo === null){
				$this->$pdo = new \PDO($conf['db']['dns'], $conf['db']['username'], $conf['db']['password'], $conf['db']['options']);
			}
		}
	}

	class PDOConfigurated extends \PDO{
		function __construct(){
			global $conf;

			$dsn = "pgsql:host={$conf['db']['host']};dbname={$conf['db']['dbname']}";

			parent::__construct($dsn, $conf['db']['username'], $conf['db']['password']
				,array(
					  static::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
					, static::ATTR_EMULATE_PREPARES => false
					, static::ATTR_AUTOCOMMIT => false
					, static::ATTR_ORACLE_NULLS => \PDO::NULL_EMPTY_STRING
				)
			);
		}

		public function getCurrentTimestamp($forjs = false, $withOutSec = false){
			$stmt = $this->prepare("SELECT ".(($forjs)? PDO::getJsDate('CURRENT_TIMESTAMP', $withOutSec) : 'CURRENT_TIMESTAMP'));
			$stmt->execute();
			return $stmt->fetch(\PDO::FETCH_COLUMN);
		}
	}

	class PDO {
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


		public static function getJsDate($field, $withOutSec = false){
			$format = (!$withOutSec)? 'YYYY-MM-DD"T"HH24:MI:SS"Z"' : 'YYYY-MM-DD"T"HH24:MI"Z"';
			return "to_char({$field}::timestamp with time zone at time zone 'Z', '{$format}')";
			//$format = (!$withOutSec)? '%Y-%m-%dT%TZ' : '%Y-%m-%dT%H:%iZ';
			//return "DATE_FORMAT({$field}, '".$format."')";
		}

		public static function getDbDate($value){
			return $value;
			//$values = explode('T', $value);
			//$values[1] = preg_replace("/Z$/i", '', $values[1]);
			//return "{$values[0]} {$values[1]}";
		}

		public static function getInstance(){
			static $instance = null;

			if (null === $instance) {
				$instance = new PDOConfigurated();
			}

			return $instance;
		}

		protected function __construct(){
		}

		/**
		 * Private clone method to prevent cloning of the instance of the
		 * *Singleton* instance.
		 *
		 * @return void
		 */
		private function __clone(){
		}

		/**
		 * Private unserialize method to prevent unserializing of the *Singleton*
		 * instance.
		 *
		 * @return void
		 */
		private function __wakeup(){
		}
	}
?>
