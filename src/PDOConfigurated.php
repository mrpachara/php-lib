<?php
	namespace sys;

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
