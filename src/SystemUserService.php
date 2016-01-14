<?php
	namespace sys;

/* CODE from http://php.net/manual/en/function.hash-equals.php */
if (!function_exists('hash_equals')) {

    /**
     * Timing attack safe string comparison
     *
     * Compares two strings using the same time whether they're equal or not.
     * This function should be used to mitigate timing attacks; for instance, when testing crypt() password hashes.
     *
     * @param string $known_string The string of known length to compare against
     * @param string $user_string The user-supplied string
     * @return boolean Returns TRUE when the two strings are equal, FALSE otherwise.
     */
    function hash_equals($known_string, $user_string)
    {
        if (func_num_args() !== 2) {
            // handle wrong parameter count as the native implentation
            trigger_error('hash_equals() expects exactly 2 parameters, ' . func_num_args() . ' given', E_USER_WARNING);
            return null;
        }
        if (is_string($known_string) !== true) {
            trigger_error('hash_equals(): Expected known_string to be a string, ' . gettype($known_string) . ' given', E_USER_WARNING);
            return false;
        }
        $known_string_len = strlen($known_string);
        $user_string_type_error = 'hash_equals(): Expected user_string to be a string, ' . gettype($user_string) . ' given'; // prepare wrong type error message now to reduce the impact of string concatenation and the gettype call
        if (is_string($user_string) !== true) {
            trigger_error($user_string_type_error, E_USER_WARNING);
            // prevention of timing attacks might be still possible if we handle $user_string as a string of diffent length (the trigger_error() call increases the execution time a bit)
            $user_string_len = strlen($user_string);
            $user_string_len = $known_string_len + 1;
        } else {
            $user_string_len = $known_string_len + 1;
            $user_string_len = strlen($user_string);
        }
        if ($known_string_len !== $user_string_len) {
            $res = $known_string ^ $known_string; // use $known_string instead of $user_string to handle strings of diffrent length.
            $ret = 1; // set $ret to 1 to make sure false is returned
        } else {
            $res = $known_string ^ $user_string;
            $ret = 0;
        }
        for ($i = strlen($res) - 1; $i >= 0; $i--) {
            $ret |= ord($res[$i]);
        }
        return $ret === 0;
    }

}

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

		/* crypt from https://alias.io/2010/01/store-passwords-safely-with-php-and-mysql/ */
		public static function encryptPassword($password){
			$cost = 10;
			$salt = strtr(base64_encode(mcrypt_create_iv(16, MCRYPT_DEV_URANDOM)), '+', '.');
			$salt = sprintf("$2a$%02d$", $cost).$salt;
			return crypt($password, $salt);
		}

		public static function decryptPassword($password, $hash){
			return crypt($password, $hash);
		}

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
