<?php
	namespace sys;

	class GeneratorService{
		private $pdoconfigurated;
		private $config = null;

		protected $code = null;
		protected $length = null;
		protected $seperator = null;

		function __construct($pdoconfigurated, $config, $code, $seperator = null, $length = null){
			$this->pdoconfigurated = $pdoconfigurated;
			$this->config = $config;
			$this->code = $code;
			$this->seperator = ($seperator !== null)? $seperator : $this->config['seperator'];
			$this->length = ($length !== null)? $length : $this->config['length'];
		}

		function getCode($subcode = null, $reuse = ''){
			if(!$this->getPdo()->inTransaction()) throw new DataServiceException("Generator for %s must be in transaction", 500);

			$code = $this->code;
			if($subcode !== null) $code .= $this->seperator.$subcode;
			$stmt = $this->getPdo()->prepare("
				SELECT
					  generator.*
					, generator.number + 1 AS number
				FROM generator
				WHERE generator.code = :code
				LIMIT 0, 1
				FOR UPDATE
			");
			$stmt->execute(array(
				  ':code' => $code
			));

			$generator = $stmt->fetch(\PDO::FETCH_ASSOC);
			if(empty($generator)) $generator = array();
			if(!array_key_exists('reuse', $generator) || ($generator['reuse'] != $reuse)){
				$generator = array_merge($generator, array(
					  'code' => $code
					, 'reuse' => $reuse
					, 'length' => $this->length
					, 'number' => 1
				));
			}

			if(empty($generator['id'])){
				$stmt = $this->getPdo()->prepare("
					INSERT INTO generator (
						  code
						, reuse
						, length
						, number
					) VALUES (
						  :code
						, :reuse
						, :length
						, :number
					)
				");
				$stmt->execute(array(
					  ':code' => $generator['code']
					, ':reuse' => $generator['reuse']
					, ':length' => $generator['length']
					, ':number' => $generator['number']
				));
			} else{
				$stmt = $this->getPdo()->prepare("
					UPDATE generator
					SET
						  generator.reuse = :reuse
						, generator.number = :number
					WHERE generator.id = :id
				");
				$stmt->execute(array(
					  ':reuse' => $generator['reuse']
					, ':number' => $generator['number']
					, ':id' => $generator['id']
				));
			}

			$generator['number'] = str_pad($generator['number'], $generator['length'], "0", STR_PAD_LEFT);

			return $generator['code']
				.$this->seperator
				.$generator['reuse']
				.$this->seperator
				.$generator['number'];
		}

		public function getCodeByDateReuse($subcode = null){
			$stmt = $this->getPdo()->prepare("
				SELECT DATE_FORMAT(CURRENT_TIMESTAMP, '%Y-%m-%dT%TZ')
			");
			$stmt->execute();
			$date = new \DateTime($stmt->fetchColumn());
			$date->setTimeZone(new \DateTimeZone($this->config['timezone']));

			return $this->getCode($subcode, $date->format('Ymd'));
		}

		protected function getPdo(){
			return $this->pdoconfigurated->getInstance();
		}
	}
?>
