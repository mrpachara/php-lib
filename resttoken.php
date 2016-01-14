<?php
	namespace sys;

	class RestTokenException extends HttpException {
		function __construct($message, $code = null, $previous = null){
			parent::__construct($message, $code, $previous);
		}
	}

	class RestToken{
		const MAX_GENERATE_TRY = 5;

		public static function generateToken(){
			$bytes = openssl_random_pseudo_bytes(16);
			return bin2hex($bytes);
		}

		protected static function extractCredentialFromDb($conf, $data){
			if(empty($data)) return null;

			return array(
				  'id' => $data['id_credential']
				, 'username' => $data['username']
				, 'fullname' => $data['fullname']
			);
		}

		protected static function extractCredentialFromUser($conf, $user){
			if(empty($user)) return null;

			return array(
				  'id' => $user['id']
				, 'username' => $user['username']
				, 'fullname' => $user['fullname']
			);
		}

		protected static function extractGrantsFromUser($conf, $user){
			if(empty($user) || empty($user['roles'])) return null;

			$extendedgrants = array_merge(
				  (array)$user['roles']
				, (!empty($conf['authoz']['default']))? (array)$conf['authoz']['default'] : array()
				, (($user['username'] == $conf['authoz']['superusername']) && !empty($conf['authoz']['superuserrole']))? (array)$conf['authoz']['superuserrole'] : array()
			);

			return array_unique($extendedgrants);
		}

		private $conf = null;
		private $userService = null;

		private $token = null;
		private $credential = null;
		private $client = null;
		private $grants = null;
		private $modes = null;

		private $pdoconfigurated = null;

		function __construct($conf, $pdoconfigurated, $userService){
			$this->conf = $conf;
			$this->pdoconfigurated = $pdoconfigurated;
			$this->userService = $userService;

			$req_headers = apache_request_headers();

			if(isset($req_headers['Authorization'])){
				$authorization_headers = explode(' ', preg_replace('/\s\s+/', ' ', $req_headers['Authorization']), 2);
				if(isset($authorization_headers[1])){
					if(strtolower($authorization_headers[0]) == 'basic'){
						// TODO: check client
						$basics = explode(':', base64_decode($authorization_headers[1]), 2);
						$this->setClient($basics[0]);
					} else if(strtolower($authorization_headers[0]) == 'bearer'){
						$this->setToken(base64_decode($authorization_headers[1]));

						$this->init();
					}
				}
			}
		}

		protected function init(){
			try {
				$stmt = $this->getPdo()->prepare("SELECT * FROM resttoken WHERE id = :token LIMIT 1");
				$stmt->execute(array(
					  ':token' => $this->token
				));

				$data = $stmt->fetch(\PDO::FETCH_ASSOC);
				if(empty($data)) throw new RestTokenException('Token is gone', 410);

				$token = $data['id'];
				$credential = static::extractCredentialFromDb($this->conf, $data);

				$stmt = $this->getPdo()->prepare("SELECT DISTINCT resttoken_grant.grant FROM resttoken_grant WHERE id_resttoken = :id_resttoken");
				$stmt->execute(array(
					  ':id_resttoken' => $token
				));
				$grants = $stmt->fetchAll(\PDO::FETCH_COLUMN);

				$stmt = $this->getPdo()->prepare("SELECT DISTINCT resttoken_grant.id_group FROM resttoken_grant WHERE (resttoken_grant.id_group > 0) AND (id_resttoken = :id_resttoken)");
				$stmt->execute(array(
					  ':id_resttoken' => $token
				));
				$modes = $stmt->fetchAll(\PDO::FETCH_COLUMN);

				$this->setData($token, $credential, $data['client'], $grants, $modes);
			} catch(RestTokenException $excp){
				if($excp->getCode() != 410) throw new RestTokenException('Invalid Token', 403, $excp);
			} catch(\Exception $excp){
				throw new RestTokenException('Invalid Token', 403, $excp);
			}
		}

		public function create($client, $credential, $grants){
			$token = null;
			for($i = 0; $i < static::MAX_GENERATE_TRY; $i++){
				$token = static::generateToken();
				try{
					$this->getPdo()->beginTransaction();
					$stmt = $this->getPdo()->prepare('
						INSERT INTO resttoken (
							  id
							, id_credential
							, username
							, fullname
							, client
						) VALUES (
							  :id
							, :id_credential
							, :username
							, :fullname
							, :client
						)
					');
					$stmt->execute(array(
						  ':id' => $token
						, ':id_credential' => $credential['id']
						, ':username' => $credential['username']
						, ':fullname' => $credential['fullname']
						, ':client' => $client
					));

					$stmt = $this->getPdo()->prepare('
						INSERT INTO resttoken_grant (
							  resttoken_grant.id_resttoken
							, resttoken_grant.grant
						) VALUES (
							  :id_resttoken
							, :grant
						)
					');

					foreach((array)$grants as $grant){
						$stmt->execute(array(
							  ':id_resttoken' => $token
							, ':grant' => $grant
						));
					}

					$this->getPdo()->commit();
					break;
				} catch(\Exception $excp){
					echo $excp;
					$this->getPdo()->rollBack();
				}
			}

			return ($i < static::MAX_GENERATE_TRY)? $token : null;
		}

		public function destroy(){
			if(empty($this->token)) return false;

			$this->getPdo()->beginTransaction();
			try{
				$stmt = $this->getPdo()->prepare('DELETE FROM resttoken_grant WHERE id_resttoken = :id_resttoken');
				$stmt->execute(array(
					  ':id_resttoken' => $this->token
				));
				$stmt = $this->getPdo()->prepare('DELETE FROM resttoken WHERE id = :token');
				$stmt->execute(array(
					  ':token' => $this->token
				));
			} catch(\Exception $excp){
				$this->getPdo()->rollBack();
				throw $excp;
			}

			if($this->getPdo()->commit()){
				$this->init();
				return true;
			} else{
				return false;
			}
		}

		public function getAll(){
			$stmt = $this->getPdo()->prepare('SELECT * FROM resttoken');
			$stmt->excute();

			return $stmt->fetch(\PDO::FETCH_ASSOC);
		}

		public function getToken(){
			return $this->token;
		}

		protected function setToken($token){
			$this->token = $token;
		}

		public function getCredential(){
			return $this->credential;
		}

		protected function setCredential($credential){
			$this->credential = $credential;
		}

		public function getClient(){
			return $this->client;
		}

		protected function setClient($client){
			$this->client = $client;
		}

		public function getGrants(){
			return $this->grants;
		}

		protected function setGrants($grants){
			$this->grants = $grants;
		}

		public function getModes(){
			return $this->modes;
		}

		protected function setModes($modes){
			$this->modes = $modes;
		}

		protected function setData($token, $credential, $client, $grants, $modes){
			$this->setToken($token);
			$this->setCredential($credential);
			$this->setClient($client);
			$this->setGrants($grants);
			$this->setModes($modes);
		}

		public function login($client, $username, $password){
			if(empty($client) || empty($username) || empty($password)) return null;

			$user = $this->userService->getUserByUsernameAndPassword($username, $password);
			if(!empty($user)){
				$credential = static::extractCredentialFromUser($this->conf, $user);
				$grants = static::extractGrantsFromUser($this->conf, $user);
				$token = $this->create($client, $credential, $grants);
				$this->setToken($token);

				if($token !== null){
					$this->init();
				}
				return $token;
			}

			return null;
		}

		public function grant($mode, $password){
			$group = $this->userService->getGroupForUser($this->getCredential()['id'], $mode, $password);

			if(!empty($group) && (!in_array($group['id'], $this->getModes()))){
				$this->getPdo()->beginTransaction();
				try{
					$stmt = $this->getPdo()->prepare("
						INSERT INTO resttoken_grant (
							  resttoken_grant.id_resttoken
							, resttoken_grant.grant
							, resttoken_grant.id_group
						) VALUES (
							  :id_resttoken
							, :grant
							, :id_group
						)
					");
					foreach($group['roles'] as $grant){
						$stmt->execute(array(
							  ':id_resttoken' => $this->getToken()
							, ':grant' => $grant
							, ':id_group' => $group['id']
						));
					}
				} catch(\Exception $excp){
					$this->getPdo()->rollBack();
					throw $excp;
				}

				$this->getPdo()->commit();

				$this->init();
			}

			return $group;
		}

		public function revoke($mode){
			if(empty($mode)) return false;

			$this->getPdo()->beginTransaction();
			try{
				$stmt = $this->getPdo()->prepare("
					DELETE FROM resttoken_grant
					WHERE resttoken_grant.id_group = (
						SELECT
						  accgroup.id
						FROM accgroup
							LEFT JOIN account ON (accgroup.id_account = account.id)
						WHERE account.code = :code
					)
				");

				$stmt->execute(array(
					  ':code' => $mode
				));
			} catch(\Exception $excp){
				$this->getPdo()->rollBack();
				throw $excp;
			}

			if($this->getPdo()->commit()){
				$this->init();
				return true;
			} else{
				return false;
			}
		}

		public function authoz(){
			$roles = func_get_args();

			if(empty($roles) && !empty($this->conf['authoz']['default'])) $roles = (array)$this->conf['authoz']['default'];
			$roles = (array)$roles;

			if(!empty($this->conf['authoz']['superuserrole'])) $roles = array_merge($roles, (array)$this->conf['authoz']['superuserrole']);

			$grants = $this->getGrants();
			$tmpintersect = null;
			if(!empty($grants)) $tmpintersect = array_intersect($roles, $grants);

			return !empty($tmpintersect);
		}

		public function authozExcp(){
			if(!call_user_func_array(array($this, 'authoz'), func_get_args())) throw new HttpForbiddenException();
			return true;
		}

		public function isSuperUser(){
			return $this->authoz($this->conf['authoz']['superuserrole']);
		}

		public function isUser($id){
			return ($id === $this->getCredential()['id']);
		}

		public function getAllowedGrants(){
			$allroles = array_keys($this->conf['authoz']['rolenames']);

			return array_values(array_diff(
				  $allroles
				, ($this->isSuperUser())? array() : (array)$this->conf['authoz']['specialroles']
			));
		}
	}
?>
