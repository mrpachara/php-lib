<?php
	namespace sys;

	class SystemUserService {
		const SQL_SELECT_USER = "
SELECT
	  accuser.id AS id
	, accuser.id_account AS id_account
	, acc_user.code AS username
	, acc_user.name AS fullname
	, accuser.passwd AS passwd
FROM accuser
	LEFT JOIN account AS acc_user ON (accuser.id_account = acc_user.id)
		";

		private $pdoconfigurate;

		function __construct($pdoconfigurate){
			$this->pdoconfigurate = $pdoconfigurate;
		}

		protected function getPdo(){
			return $this->pdoconfigurate->getInstance();
		}

		private function getDeepRoles($id_account, &$id_accgroups, &$mode_accgroups){
			$roles = array();

			if(!empty($id_account)){
				$stmt = $this->getPdo()->prepare('SELECT account_role.role FROM account_role WHERE id_account = :id_account');
				$stmt->execute(array(
					 ':id_account' => $id_account
				));

				$roles = $stmt->fetchAll(\PDO::FETCH_COLUMN);

				$stmt = $this->getPdo()->prepare("
					SELECT
						  accgroup.*
						, account.code AS code
						, account.name AS name
					FROM accgroup_account
						LEFT JOIN accgroup ON (accgroup_account.id_accgroup = accgroup.id)
						LEFT JOIN account ON (accgroup.id_account = account.id)
					WHERE accgroup_account.id_account = :id_account
				");
				$stmt->execute(array(
					  ':id_account' => $id_account
				));

				while($accgroup = $stmt->fetch(\PDO::FETCH_ASSOC)){
					if(!in_array($accgroup['id'], $id_accgroups)){
						$id_accgroups[] = $accgroup['id'];

						if($accgroup['passwd'] === null){
							$roles = array_merge(
								  $roles
								, $this->getDeepRoles($accgroup['id_account'], $id_accgroups, $mode_accgroups)
							);
						} else{
							$mode_accgroups[] = array(
								  'id' => $accgroup['id']
								, 'code' => $accgroup['code']
								, 'name' => $accgroup['name']
							);
						}
					}
				}
			}

			return $roles;
		}

		protected function prepareUser($user){
			if(empty($user)) return $user;

			unset($user['passwd']);

			$roles = array();
			$modes = array();

			if(!empty($user['id_account'])){
				$id_accgroups = array();
				$roles = $this->getDeepRoles($user['id_account'], $id_accgroups, $modes);
			}

			$user['roles'] = array_unique($roles);
			$user['modes'] = $modes;
			return $user;
		}

		protected function prepareGroup($group){
			if(empty($group)) return $group;

			unset($group['passwd']);

			$roles = array();
			$modes = array();

			if(!empty($group['id_account'])){
				$id_accgroups = array();
				$roles = $this->getDeepRoles($group['id_account'], $id_accgroups, $modes);
			}

			$group['roles'] = array_unique($roles);
			$group['modes'] = $modes;
			return $group;
		}

		public function getUser($id){
			if(empty($id)) return null;

			try{
				$stmt = $this->getPdo()->prepare(static::SQL_SELECT_USER.'
					WHERE (accuser.id = :id) AND (NOT accuser.terminate)
					LIMIT 1
				');
				$stmt->execute(array(
					  ':id' => $id
				));

				return $this->prepareUser($stmt->fetch(\PDO::FETCH_ASSOC));
			} catch(\PDOException $excp){
			}

			return null;
		}

		public function getUserByUsernameAndPassword($username, $password){
			if(empty($username) || empty($password)) return null;

			try{
				$stmt = $this->getPdo()->prepare(static::SQL_SELECT_USER.'
					WHERE
						    (acc_user.code = :username)
						AND (NOT accuser.terminate)
					LIMIT 1
				');
				$stmt->execute(array(
					 ':username' => $username
				));

				$user = $stmt->fetch(\PDO::FETCH_ASSOC);
				if(!empty($user) && hash_equals($user['passwd'], static::decryptPassword($password, $user['passwd']))){
					return $this->prepareUser($user);
				}
			} catch(\PDOException $excp){
				echo $excp;
			}

			return null;
		}

		public function getGroupForUser($id, $mode, $password){
			$user = $this->getUser($id);
			if(empty($user)) return null;

			foreach($user['modes'] as $user_mode){
				if($mode == $user_mode['code']){
					$stmt = $this->getPdo()->prepare("
						SELECT
							  accgroup.*
							, account.code AS code
							, account.name AS name
						FROM accgroup
							LEFT JOIN account ON (accgroup.id_account = account.id)
						WHERE account.code = :code
						LIMIT 1
					");
					$stmt->execute(array(
						  ':code' => $mode
					));

					$accgroup = $stmt->fetch(\PDO::FETCH_ASSOC);
					if(!empty($accgroup) && hash_equals($accgroup['passwd'], static::decryptPassword($password, $accgroup['passwd']))){
						return $this->prepareGroup($accgroup);
					}
				}
			}

			return null;
		}
	}
?>
