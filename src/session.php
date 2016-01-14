<?php
	namespace sys;

	class Sessions implements \SessionHandlerInterface{
		private $conf = null;
		private $userService = null;
		private $user = null;

		public static function forbidden_html($code, $message){
			header("Content-Type: text/html; charset=utf-8");

			return $message;
		}

		public static function forbidden_json($code, $message){
			header("Content-Type: application/json; charset=utf-8");

			return json_encode(array(
				  'errors' => array(
					  new \Exception($message, $code)
				)
			));
		}

		function __construct($conf, $userService){
			$this->conf = $conf;

			if(isset($this->conf['session']['gc_probability'])) ini_set('session.gc_probability', $this->conf['session']['gc_probability']);
			if(isset($this->conf['session']['gc_divisor'])) ini_set('session.gc_divisor', $this->conf['session']['gc_divisor']);
			if(isset($this->conf['session']['gc_maxlifetime'])) ini_set('session.gc_maxlifetime', $this->conf['session']['gc_maxlifetime']);

			$this->pdo = new PDOConfigurated();
			$this->userService = $userService;

			session_set_save_handler($this, true);

			session_name($this->conf['session']['name']);
			session_start();
		}

		protected function extendSessoinRole(&$user){
			if(empty($user)) return;

			$user['roles'] = array_merge(
				 (!empty($user['roles']))? (array)$user['roles'] : array()
				,(!empty($this->conf['authoz']['default']))? (array)$this->conf['authoz']['default'] : array()
				,(($user['username'] == $this->conf['authoz']['superusername']) && !empty($this->conf['authoz']['superuserrole']))? (array)$this->conf['authoz']['superuserrole'] : array()
			);

			$user['roles'] = array_unique($user['roles']);
		}

		// return bool
		public function open($save_path, $name){
			return $this->pdo->beginTransaction();
		}

		// return string
		public function read($session_id){
			if(!empty($this->excp)) return false;

			try{
				$stmt = $this->pdo->prepare('
					SELECT
						  id AS id
						, '.\sys\PDO::getJsDate('expires').' AS expires
						, data AS data
						, id_user AS id_user
					FROM sessions WHERE ((id = :id) AND (expires > CURRENT_TIMESTAMP)) FOR UPDATE
				');
				$stmt->execute(array(
					  ':id' => $session_id
				));
				$session = $stmt->fetch(\PDO::FETCH_ASSOC);

				if(!empty($session['id_user'])){
					$this->user = $this->userService->getUser($session['id_user']);
					static::extendSessoinRole($this->user);
				}

				return (!empty($session))? $session['data'] : null;
			} catch(\PDOException $excp){
				$this->excp = $excp;
			}

			return null;
		}

		// return bool
		public function write($session_id , $session_data){
			//echo "<pre>session->write:".time()."</pre>";
			if(!empty($this->excp)) return false;

			try{
				$stmt = $this->pdo->prepare('UPDATE "sessions" SET "expires" = (CURRENT_TIMESTAMP + :maxlifetime::interval), "data" = :data, "id_user" = :id_user  WHERE "id" = :id;');
				$stmt->execute(array(
					  ':id' => $session_id
					, ':maxlifetime' => ini_get('session.gc_maxlifetime').' second'
					, ':data' => $session_data
					, ':id_user' => (!empty($this->user['id']))? $this->user['id'] : null
				));

				return ($stmt->rowCount() == 1);
			} catch(\PDOException $excp){
				$this->excp = $excp;
			}

			return false;
		}

		// return bool
		public function destroy($session_id){
			if(!empty($this->excp)) return false;

			try{
				$stmt = $this->pdo->prepare('DELETE FROM "sessions" WHERE "id" = :id');
				$stmt->execute(array(
					  ':id' => $session_id
				));

				return ($stmt->rowCount() == 1);
			} catch(\PDOException $excp){
				$this->excp = $excp;
			}

			return false;
		}

		// return bool
		public function gc($maxlifetime){
			if(!empty($this->excp)) return false;

			try{
				$stmt = $this->pdo->prepare('DELETE FROM "sessions" WHERE "expires" < CURRENT_TIMESTAMP;');
				$stmt->execute();

				return true;
			} catch(\PDOException $excp){
				$this->excp = $excp;
			}

			return false;
		}

		// return bool
		public function close(){
			$return = null;
			if(empty($this->excp)){
				$return = $this->pdo->commit();
			} else{
				$return = $this->pdo->rollBack();
			}
			$this->pdo = null;

			return $return;
		}

		public function getAll($page = null, $search = null){
			if(!empty($this->excp)) return;

			$stmt = $this->pdo->prepare('SELECT "sessions"."id", "expires", "user"."username", "user"."id" as id_user FROM "sessions" LEFT JOIN "user" ON("sessions"."id_user" = "user"."id")');
			$stmt->execute();

			return $stmt->fetchAll(\PDO::FETCH_ASSOC);
		}

		public function create($session_id){
			if(!empty($this->excp)) return false;

			try{
				$stmt = $this->pdo->prepare('DELETE FROM "sessions" WHERE "id" = :id;');
				$stmt->execute(array(
					  ':id' => $session_id
				));

				$stmt = $this->pdo->prepare('INSERT INTO "sessions" ("id") VALUES (:id);');
				$stmt->execute(array(
					  ':id' => $session_id
				));

				return ($stmt->rowCount() == 1);
			} catch(\PDOException $excp){
				$this->excp = $excp;
			}

			return false;
		}

		public function getUser(){
			return $_SESSION[$this->conf['session']['sysns']]['user'];
		}

		private function setUser($user){
			$_SESSION[$this->conf['session']['sysns']]['user'] = $user;
		}

		public function login($username, $password){
			$this->setUser($this->userService->getUserByUsernameAndPassword($username, $password));
			static::extendSessoinRole($_SESSION[$this->conf['session']['sysns']]['user']);

			return !empty($_SESSION[$this->conf['session']['sysns']]['user']);
		}

		public function authoz($roles = null){
			if(($roles === null) && !empty($this->conf['authoz']['default'])) $roles = (array)$this->conf['authoz']['default'];
			$roles = (array)$roles;

			if(!empty($this->conf['authoz']['superuserrole'])) $roles = array_merge($roles, (array)$this->conf['authoz']['superuserrole']);

			$user = $this->getUser();
			$tmpintersect = null;
			if(!empty($user)) $tmpintersect = array_intersect($roles, (array)$user['roles']);

			return !empty($tmpintersect);
		}

		function authozPage($roles = null, $message_func = null){
			if(!$this->authoz($roles)){
				header(
					 ((isset($_SERVER['SERVER_PROTOCOL']))? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0')
					." {$this->conf['authoz']['forbidden_code']} {$this->conf['authoz']['forbidden_message']}"
				);
				exit(call_user_func(($message_func === null)? 'static::forbidden_html' : $message_func, $this->conf['authoz']['forbidden_code'], $this->conf['authoz']['forbidden_message']));
			}
		}

		function getAllowedRoles(){
			return array_merge(
				 (array)$this->conf['authoz']['allowedroles']
				,($this->authoz($this->conf['authoz']['superuserrole']))? (array)$this->conf['authoz']['specialroles'] : array()
			);
		}
	}
?>
