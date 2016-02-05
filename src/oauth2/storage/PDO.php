<?php
	namespace sys\oauth2\storage;

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

	use OAuth2\OpenID\Storage\UserClaimsInterface as UserClaimsInterface;
	use OAuth2\OpenID\Storage\AuthorizationCodeInterface as OpenIDAuthorizationCodeInterface;

	use OAuth2\Storage\AuthorizationCodeInterface as AuthorizationCodeInterface;
	use OAuth2\Storage\AccessTokenInterface;
	use OAuth2\Storage\ClientCredentialsInterface;
	use OAuth2\Storage\UserCredentialsInterface;
	use OAuth2\Storage\RefreshTokenInterface;
	use OAuth2\Storage\JwtBearerInterface;
	use OAuth2\Storage\ScopeInterface;
	use OAuth2\Storage\PublicKeyInterface;

	/**
	 * Simple PDO storage for all storage types
	 *
	 * NOTE: This class is meant to get users started
	 * quickly. If your application requires further
	 * customization, extend this class or create your own.
	 *
	 * NOTE: Passwords are stored in plaintext, which is never
	 * a good idea.  Be sure to override this for your application
	 *
	 * @author Brent Shaffer <bshafs at gmail dot com>
	 */
	class PDO implements
		AuthorizationCodeInterface,
		AccessTokenInterface,
		ClientCredentialsInterface,
		UserCredentialsInterface,
		RefreshTokenInterface,
		JwtBearerInterface,
		ScopeInterface,
		PublicKeyInterface,
		UserClaimsInterface,
		OpenIDAuthorizationCodeInterface
	{
		public static function encryptPassword($password){
			$cost = 10;
			$salt = strtr(base64_encode(openssl_random_pseudo_bytes(16)), '+', '.');
			$salt = sprintf("$2y$%02d$", $cost).$salt;
			return crypt($password, $salt);
		}

		public static function decryptPassword($password, $hash){
			return crypt($password, $hash);
		}

		private $conf;
		protected $pdoconfigurated;
		protected $config;

		public function __construct($conf, $pdoconfigurated, $config = []){
			$this->conf = $conf;
			$this->pdoconfigurated = $pdoconfigurated;

			$this->config = array_merge([
				'account' => 'account',
				'sys.account' => 'sys.account',
				'sys.accountrole' => 'sys.accountrole',
				'sys.client' => 'sys.client',
				'sys.clientgranttype' => 'sys.clientgranttype',
				'sys.group' => 'sys.group',
				'sys.user' => 'sys.user',
				'auth.accesstoken' => 'auth.accesstoken',
				'auth.refreshtoken' => 'auth.refreshtoken',
				'auth.authzcode' => 'auth.authzcode',
			], $config);
/*
			$this->config = array_merge(array(
				'client_table' => 'oauth_clients',
				'access_token_table' => 'oauth_access_tokens',
				'refresh_token_table' => 'oauth_refresh_tokens',
				'code_table' => 'oauth_authorization_codes',
				'user_table' => 'oauth_users',
				'jwt_table'  => 'oauth_jwt',
				'jwt_table'  => 'accclient',
				'jti_table'  => 'oauth_jti',
				'scope_table'  => 'oauth_scopes',
				'public_key_table'  => 'accclient',
			), $config);
*/
		}

		private function getDeepRoles($id_account, &$id_accgroups){
			$roles = [];

			if(!empty($id_account)){
				$stmt = $this->getPdo()->prepare("
					SELECT
						  sys_accountrole.role
					FROM {$this->config['sys.accountrole']} AS sys_accountrole
					WHERE sys_accountrole.id_sys_account = :id_account
				");
				$stmt->execute(compact('id_account'));

				$roles = $stmt->fetchAll(\PDO::FETCH_COLUMN);
				$stmt->closeCursor();

				$stmt = $this->getPdo()->prepare("
					SELECT
						  sys_groupaccount.id_sys_group AS id_sys_group
						, sys_group.id_sys_account AS id_sys_account
					FROM {$this->config['sys.groupaccount']} AS sys_groupaccount
						INNER JOIN {$this->config['sys.group']} AS sys_group ON (sys_groupaccount.id_sys_group = sys_group.id)
					WHERE sys_groupaccount.id_sys_account = :id_account
				");
				$stmt->execute([
					':id_account' => $id_account,
				]);

				$accgroups = [];
				while($accgroup = $stmt->fetch(\PDO::FETCH_ASSOC)){
					if(!in_array($id_accgroup, $id_accgroups)){
						$id_accgroups[] = $accgroup['id_sys_group'];
						$accgroups[] = $accgroup;
					}
				}
				$stmt->closeCursor();

				foreach($id_accgroups_new as $id_accgroup){
					$roles = array_merge(
						  $roles
						, $this->getDeepRoles($accgroup['id_sys_account'], $id_accgroups)
					);
				}
			}

			return $roles;
		}

		protected function prepareRole(&$acc){
			if(empty($acc)) return $acc;

			$roles = [];

			if(!empty($acc['id_sys_account'])){
				$id_groups_prepared = [];
				$id_accounts = [$acc['id_sys_account']];

				$stmt_role = $this->getPdo()->prepare("
					SELECT
						  sys_accountrole.role
					FROM {$this->config['sys.accountrole']} AS sys_accountrole
					WHERE sys_accountrole.id_sys_account = :id_account
				");

				$stmt_group = $this->getPdo()->prepare("
					SELECT
						  sys_group.id_sys_account
					FROM {$this->config['sys.groupaccount']} AS sys_groupaccount
						INNER JOIN {$this->config['sys.group']} AS sys_group ON (sys_groupaccount.id_sys_group = sys_group.id)
					WHERE sys_groupaccount.id_sys_account = :id_account
				");

				for($i = 0; $i < count($id_accounts); $i++){
					$stmt_role->execute([
						'id_account' => $id_accounts[$i],
					]);
					$roles = array_merge(
						  $roles
						, $stmt_role->fetchAll(\PDO::FETCH_COLUMN)
					);
					$stmt_role->closeCursor();

					$stmt_group->execute([
						'id_account' => $id_accounts[$i],
					]);
					while($id_account = $stmt_group->fetch(\PDO::FETCH_COLUMN)){
						if(!in_array($id_account, $id_accounts)) $id_accounts[] = $id_account;
					}
					$stmt_group->closeCursor();
				}
			}

			$acc['roles'] = array_unique($roles);

			return $acc;
		}

		protected function getPdo(){
			return $this->pdoconfigurated->getInstance();
		}

		/* OAuth2\Storage\ClientCredentialsInterface */
		public function checkClientCredentials($client_id, $client_secret = null){
			$result = $this->getClientDetails($client_id);

			// make this extensible
			return $result && $result['client_secret'] == $client_secret;
		}

		public function isPublicClient($client_id){
			if(!$result = $this->getClientDetails($client_id)){
				return false;
			}

			return empty($result['client_secret']);
		}

		/* OAuth2\Storage\ClientInterface */
		public function getClientDetails($client_id){
			$stmt = $this->getPdo()->prepare("
				SELECT
					  account.*
					, sys_client.*
					, account.code AS client_id
					, account.name AS client_name
					, NULL AS redirect_uri
					, CONCAT_WS(' ', (
						SELECT
							  sys_clientgranttype.granttype
						FROM {$this->config['sys.clientgranttype']} AS sys_clientgranttype
						WHERE sys_clientgranttype.id_sys_client = sys_client.id)
					) AS grant_types
				FROM {$this->config['sys.client']} AS sys_client
					INNER JOIN {$this->config['sys.account']} AS sys_account ON (sys_client.id_sys_account = sys_account.id)
					INNER JOIN {$this->config['account']} AS account ON (sys_account.id_account = account.id)
				WHERE
					account.code = :client_id
				LIMIT 1
			");
			$stmt->execute(compact('client_id'));

			$clientDetails = $stmt->fetch(\PDO::FETCH_ASSOC);
			$stmt->closeCursor();

			if(!empty($clientDetails)){
				$this->prepareRole($clientDetails);
				if(empty($clientDetails['redirect_uri'])) $clientDetails['redirect_uri'] = null;
				if(empty($clientDetails['grant_types'])) $clientDetails['grant_types'] = null;
				if(!empty($clientDetails['roles'])){
					$clientDetails['scope'] = implode(' ', $clientDetails['roles']);
				} else{
					$clientDetails['scope'] = null;
				}
			}

			return $clientDetails;
		}

		// TODO: update to new schema
		public function setClientDetails($client_id, $client_secret = null, $redirect_uri = null, $grant_types = null, $scope = null, $user_id = null){
			// if it exists, update it.
			if ($this->getClientDetails($client_id)) {
				$stmt = $this->getPdo()->prepare($sql = sprintf('UPDATE %s SET client_secret=:client_secret, redirect_uri=:redirect_uri, grant_types=:grant_types, scope=:scope, user_id=:user_id where client_id=:client_id', $this->config['client_table']));
			} else {
				$stmt = $this->getPdo()->prepare(sprintf('INSERT INTO %s (client_id, client_secret, redirect_uri, grant_types, scope, user_id) VALUES (:client_id, :client_secret, :redirect_uri, :grant_types, :scope, :user_id)', $this->config['client_table']));
			}

			$result = $stmt->execute(compact('client_id', 'client_secret', 'redirect_uri', 'grant_types', 'scope', 'user_id'));
			$stmt->closeCursor();

			return $result;
		}

		public function checkRestrictedGrantType($client_id, $grant_type){
			$details = $this->getClientDetails($client_id);
			if (isset($details['grant_types'])) {
				$grant_types = explode(' ', $details['grant_types']);

				return in_array($grant_type, (array) $grant_types);
			}

			// if grant_types are not defined, then none are restricted
			return true;
		}

		private function prepareScope(&$acc){
			if(empty($acc)) return $acc;

			$scopes = [];
			if(!empty($acc['id_auth_account'])){
				$stmt = $this->getPdo()->prepare("
					SELECT
						auth_accountscope.scope
					FROM {$this->config['auth.accountscope']} AS auth_accountscope
					WHERE auth_accountscope.id_auth_account = :id_auth_account
				");
				$stmt->execute([
					'id_auth_account' => $acc['id_auth_account'],
				]);
				$scopes = $stmt->fetchAll(\PDO::FETCH_COLUMN);
				$stmt->closeCursor();
			}

			$acc['scope'] = implode(' ', $scopes);

			return $acc;
		}

		private function updateScope($id_auth_account, $scope){
			if(!($existedTransaction = $this->getPdo()->inTransaction()) $this->getPdo()->beginTransaction();

			try{
				$stmt = $this->getPdo()->prepare("
					DELETE FROM {$this->config['auth.accountscope']} WHERE id_auth_account = :id_auth_account
				");
				$stmt->execute([
					'id_auth_account' => $id_auth_account,
				]);
				$stmt->closeCursor();

				$stmt = $this->getPdo()->prepare("
					INSERT INTO {$this->config['auth.accountscope']} (
						  id_auth_account
						, scope
					) VALUES (
						  :id_auth_account
						, :scope
					)
				");

				$scopes = explode(' ', $scope);
				foreach($scopes as $scope){
					$stmt->execute([
						'id_auth_account' => $id_auth_account,
						'scope' => $scope,
					]);
				}
				$stmt->closeCursor();
			} catch(\PDOException $excp){
				if(!$existedTransaction) $this->getPdo()->rollBack();
				throw $excp;
			}

			return (!$existedTransaction)? $this->getPdo()->commit() : true;
		}

		/* OAuth2\Storage\AccessTokenInterface */
		public function getAccessToken($access_token){
			$stmt = $this->getPdo()->prepare("
				SELECT
					  auth_account.*
					, auth_accesstoken.*
				FROM {$this->config['auth.accesstoken']} AS auth_accesstoken
					INNER JOIN {$this->config['auth.account']} AS auth_account ON (auth_accesstoken.id_auth_account = auth_account.id)
				WHERE auth_account.code = :access_token
				LIMIT 1
			");

			$stmt->execute(compact('access_token'));
			$token = $stmt->fetch(\PDO::FETCH_ASSOC);
			$stmt->closeCursor();

			if(!empty($token)) {
				// convert date string back to timestamp
				$this->prepareScope($token);
				if(empty($token['scope'])) $token['scope'] = null;
				$token['expires'] = strtotime($token['expires']);
			}

			return $token;
		}

		public function setAccessToken($access_token, $client_id, $user_id, $expires, $scope = null){
			if(!($existedTransaction = $this->getPdo()->inTransaction()) $this->getPdo()->beginTransaction();

			try{
				// convert expires to datestring
				$expires = date('Y-m-d H:i:s', $expires);

				// if it exists, update it.
				if($this->getAccessToken($access_token)){
					$stmt = $this->getPdo()->prepare("
						UPDATE {$this->config['auth.account']}
						SET
							  client_id=:client_id
							, expires=:expires
							, user_id=:user_id
						WHERE code = :access_token
					");
				} else{
					$stmt = $this->getPdo()->prepare("
						INSERT INTO {$this->config['auth.account']} (
							  code
							, client_id
							, expires
							, user_id
						) VALUES (
							  :access_token
							, :client_id
							, :expires
							, :user_id
						)
					");
				}

				$stmt->execute(compact('access_token', 'client_id', 'user_id', 'expires', 'scope'));
				$stmt->closeCursor();

				$accesstoken = $this->getAccessToken($access_token);
				$this->updateScope($accesstoken['id_auth_account'], $scope);
			} catch(\PDOException $excp){
				if(!$existedTransaction) $this->getPdo()->rollBack();
				return false;
			}

			return (!$existedTransaction)? $this->getPdo()->commit() : true;
		}

		/* OAuth2\Storage\AuthorizationCodeInterface */
		public function getAuthorizationCode($code)
		{
			$stmt = $this->getPdo()->prepare(sprintf('SELECT * from %s where authorization_code = :code', $this->config['code_table']));
			$stmt->execute(compact('code'));

			if ($code = $stmt->fetch(\PDO::FETCH_ASSOC)) {
				// convert date string back to timestamp
				$code['expires'] = strtotime($code['expires']);
			}
			$stmt->closeCursor();

			return $code;
		}

		public function setAuthorizationCode($code, $client_id, $user_id, $redirect_uri, $expires, $scope = null, $id_token = null)
		{
			if (func_num_args() > 6) {
				// we are calling with an id token
				return call_user_func_array(array($this, 'setAuthorizationCodeWithIdToken'), func_get_args());
			}

			// convert expires to datestring
			$expires = date('Y-m-d H:i:s', $expires);

			// if it exists, update it.
			if ($this->getAuthorizationCode($code)) {
				$stmt = $this->getPdo()->prepare($sql = sprintf('UPDATE %s SET client_id=:client_id, user_id=:user_id, redirect_uri=:redirect_uri, expires=:expires, scope=:scope where authorization_code=:code', $this->config['code_table']));
			} else {
				$stmt = $this->getPdo()->prepare(sprintf('INSERT INTO %s (authorization_code, client_id, user_id, redirect_uri, expires, scope) VALUES (:code, :client_id, :user_id, :redirect_uri, :expires, :scope)', $this->config['code_table']));
			}

			$result = $stmt->execute(compact('code', 'client_id', 'user_id', 'redirect_uri', 'expires', 'scope'));
			$stmt->closeCursor();

			return $result;
		}

		private function setAuthorizationCodeWithIdToken($code, $client_id, $user_id, $redirect_uri, $expires, $scope = null, $id_token = null)
		{
			// convert expires to datestring
			$expires = date('Y-m-d H:i:s', $expires);

			// if it exists, update it.
			if ($this->getAuthorizationCode($code)) {
				$stmt = $this->getPdo()->prepare($sql = sprintf('UPDATE %s SET client_id=:client_id, user_id=:user_id, redirect_uri=:redirect_uri, expires=:expires, scope=:scope, id_token =:id_token where authorization_code=:code', $this->config['code_table']));
			} else {
				$stmt = $this->getPdo()->prepare(sprintf('INSERT INTO %s (authorization_code, client_id, user_id, redirect_uri, expires, scope, id_token) VALUES (:code, :client_id, :user_id, :redirect_uri, :expires, :scope, :id_token)', $this->config['code_table']));
			}

			$result = $stmt->execute(compact('code', 'client_id', 'user_id', 'redirect_uri', 'expires', 'scope', 'id_token'));
			$stmt->closeCursor();

			return $result;
		}

		public function expireAuthorizationCode($code)
		{
			$stmt = $this->getPdo()->prepare(sprintf('DELETE FROM %s WHERE authorization_code = :code', $this->config['code_table']));

			$result = $stmt->execute(compact('code'));
			$stmt->closeCursor();

			return $result;
		}

		/* OAuth2\Storage\UserCredentialsInterface */
		public function checkUserCredentials($username, $password)
		{
			if ($user = $this->getUser($username)) {
				return $this->checkPassword($user, $password);
			}

			return false;
		}

		public function getUserDetails($username)
		{
			return $this->getUser($username);
		}

		/* UserClaimsInterface */
		public function getUserClaims($user_id, $claims)
		{
			if (!$userDetails = $this->getUserDetails($user_id)) {
				return false;
			}

			$claims = explode(' ', trim($claims));
			$userClaims = array();

			// for each requested claim, if the user has the claim, set it in the response
			$validClaims = explode(' ', self::VALID_CLAIMS);
			foreach ($validClaims as $validClaim) {
				if (in_array($validClaim, $claims)) {
					if ($validClaim == 'address') {
						// address is an object with subfields
						$userClaims['address'] = $this->getUserClaim($validClaim, $userDetails['address'] ?: $userDetails);
					} else {
						$userClaims = array_merge($userClaims, $this->getUserClaim($validClaim, $userDetails));
					}
				}
			}

			return $userClaims;
		}

		protected function getUserClaim($claim, $userDetails)
		{
			$userClaims = array();
			$claimValuesString = constant(sprintf('self::%s_CLAIM_VALUES', strtoupper($claim)));
			$claimValues = explode(' ', $claimValuesString);

			foreach ($claimValues as $value) {
				$userClaims[$value] = isset($userDetails[$value]) ? $userDetails[$value] : null;
			}

			return $userClaims;
		}

		/* OAuth2\Storage\RefreshTokenInterface */
		public function getRefreshToken($refresh_token)
		{
			$stmt = $this->getPdo()->prepare(sprintf('SELECT * FROM %s WHERE refresh_token = :refresh_token', $this->config['refresh_token_table']));

			$token = $stmt->execute(compact('refresh_token'));
			if ($token = $stmt->fetch(\PDO::FETCH_ASSOC)) {
				// convert expires to epoch time
				$token['expires'] = strtotime($token['expires']);
			}
			$stmt->closeCursor();

			return $token;
		}

		public function setRefreshToken($refresh_token, $client_id, $user_id, $expires, $scope = null)
		{
			// convert expires to datestring
			$expires = date('Y-m-d H:i:s', $expires);

			$stmt = $this->getPdo()->prepare(sprintf('INSERT INTO %s (refresh_token, client_id, user_id, expires, scope) VALUES (:refresh_token, :client_id, :user_id, :expires, :scope)', $this->config['refresh_token_table']));

			$result = $stmt->execute(compact('refresh_token', 'client_id', 'user_id', 'expires', 'scope'));
			$stmt->closeCursor();

			return $result;
		}

		public function unsetRefreshToken($refresh_token)
		{
			$stmt = $this->getPdo()->prepare(sprintf('DELETE FROM %s WHERE refresh_token = :refresh_token', $this->config['refresh_token_table']));

			$result = $stmt->execute(compact('refresh_token'));
			$stmt->closeCursor();

			return $result;
		}

		// plaintext passwords are bad!  Override this for your application
		protected function checkPassword($user, $password)
		{
			return (!empty($user) && hash_equals($user['password'], static::decryptPassword($password, $user['password'])));
		}

		public function getUser($username)
		{
			$stmt = $this->getPdo()->prepare("
				SELECT
					  acc_user.code AS username
					, acc_user.name AS name
					, accuser.password AS password
					, acc_user.id AS id_account
				FROM accuser
					LEFT JOIN account AS acc_user ON (accuser.id_account = acc_user.id)
				WHERE
					acc_user.code = :code
				LIMIT 1
			");
			$stmt->execute(array(
				  ':code' => $username
			));

			if($userInfo = $stmt->fetch(\PDO::FETCH_ASSOC)){
				$this->prepareRole($userInfo);
				if(
					   !empty($this->conf['superusername'])
					&& $userInfo['username'] == $this->conf['superusername']
					&& !empty($this->conf['superuserrole'])
				){
					$userInfo['roles'] = array_merge($userInfo['roles'], (array)$this->conf['superuserrole']);
				}

				if(!empty($userInfo['roles'])){
					$userInfo['scope'] = implode(' ', $userInfo['roles']);
				} else{
					$userInfo['scope'] = null;
				}
			} else{
				$stmt->closeCursor();
				return false;
			}
			$stmt->closeCursor();

			// the default behavior is to use "username" as the user_id
			return array_merge(array(
				  'user_id' => $username
			), $userInfo);
		}

		public function setUser($username, $password, $firstName = null, $lastName = null)
		{
			/*
			// do not store in plaintext
			$password = sha1($password);

			// if it exists, update it.
			if ($this->getUser($username)) {
				$stmt = $this->getPdo()->prepare($sql = sprintf('UPDATE %s SET password=:password, first_name=:firstName, last_name=:lastName where username=:username', $this->config['user_table']));
			} else {
				$stmt = $this->getPdo()->prepare(sprintf('INSERT INTO %s (username, password, first_name, last_name) VALUES (:username, :password, :firstName, :lastName)', $this->config['user_table']));
			}

			$result = $stmt->execute(compact('username', 'password', 'firstName', 'lastName'));
			$stmt->closeCursor();

			return $result;
			*/
		}

		/* ScopeInterface */
		public function scopeExists($scope)
		{
			$scope = explode(' ', $scope);

			$scope_exists = array_merge(
				  (!empty($this->conf['rolenames']))? array_keys($this->conf['rolenames']) : array()
				, (!empty($this->conf['default']))? (array)$this->conf['default'] : array()
			);

			return (count(array_diff($scope, $scope_exists)) == 0);
		}

		public function getDefaultScope($client_id = null)
		{
			return implode(' ', (array)$this->conf['default']);
		}

		/* JWTBearerInterface */
		public function getClientKey($client_id, $subject)
		{
			//$stmt = $this->getPdo()->prepare($sql = sprintf('SELECT public_key from %s where client_id=:client_id AND subject=:subject', $this->config['jwt_table']));
			//$stmt->execute(array('client_id' => $client_id, 'subject' => $subject));

			$stmt = $this->getPdo()->prepare($sql = sprintf("
				SELECT
					  key_public
				FROM %s AS accclient
					LEFT JOIN account AS acc_client ON(accclient.id_account = acc_client.id)
				WHERE
					acc_client.code = :client_id
			"
			, $this->config['jwt_table'])
			);

			$stmt->execute(['client_id' => $client_id]);
			$result = $stmt->fetchColumn();
			$stmt->closeCursor();

			return $result;
		}

		public function getClientScope($client_id)
		{
			if (!$clientDetails = $this->getClientDetails($client_id)) {
				return false;
			}

			if (isset($clientDetails['scope'])) {
				return $clientDetails['scope'];
			}

			return null;
		}

		public function getJti($client_id, $subject, $audience, $expires, $jti)
		{
			$stmt = $this->getPdo()->prepare($sql = sprintf('SELECT * FROM %s WHERE issuer=:client_id AND subject=:subject AND audience=:audience AND expires=:expires AND jti=:jti', $this->config['jti_table']));

			$stmt->execute(compact('client_id', 'subject', 'audience', 'expires', 'jti'));

			if ($result = $stmt->fetch(\PDO::FETCH_ASSOC)) {
				$stmt->closeCursor();
				return array(
					'issuer' => $result['issuer'],
					'subject' => $result['subject'],
					'audience' => $result['audience'],
					'expires' => $result['expires'],
					'jti' => $result['jti'],
				);
			}
			$stmt->closeCursor();

			return null;
		}

		public function setJti($client_id, $subject, $audience, $expires, $jti)
		{
			$stmt = $this->getPdo()->prepare(sprintf('INSERT INTO %s (issuer, subject, audience, expires, jti) VALUES (:client_id, :subject, :audience, :expires, :jti)', $this->config['jti_table']));

			$result = $stmt->execute(compact('client_id', 'subject', 'audience', 'expires', 'jti'));
			$stmt->closeCursor();

			return $result;
		}

		/* PublicKeyInterface */
		public function getPublicKey($client_id = null)
		{
			/*
			$stmt = $this->getPdo()->prepare($sql = sprintf('SELECT public_key FROM %s WHERE client_id=:client_id OR client_id IS NULL ORDER BY client_id IS NOT NULL DESC', $this->config['public_key_table']));

			$stmt->execute(compact('client_id'));
			if ($result = $stmt->fetch(\PDO::FETCH_ASSOC)) {
				return $result['public_key'];
			}
			*/
			$stmt = $this->getPdo()->prepare($sql = sprintf("
				SELECT
					  key_public
				FROM %s AS accclient
					LEFT JOIN account AS acc_client ON(accclient.id_account = acc_client.id)
				WHERE
					acc_client.code = :client_id
			"
			, $this->config['public_key_table'])
			);

			$stmt->execute(['client_id' => $client_id]);
			$result = $stmt->fetchColumn();
			$stmt->closeCursor();

			return $result;
		}

		public function getPrivateKey($client_id = null)
		{
			/*
			$stmt = $this->getPdo()->prepare($sql = sprintf('SELECT private_key FROM %s WHERE client_id=:client_id OR client_id IS NULL ORDER BY client_id IS NOT NULL DESC', $this->config['public_key_table']));

			$stmt->execute(compact('client_id'));
			if ($result = $stmt->fetch(\PDO::FETCH_ASSOC)) {
				return $result['private_key'];
			}
			*/
			$stmt = $this->getPdo()->prepare($sql = sprintf("
				SELECT
					  key_private
				FROM %s AS accclient
					LEFT JOIN account AS acc_client ON(accclient.id_account = acc_client.id)
				WHERE
					acc_client.code = :client_id
			"
			, $this->config['public_key_table'])
			);

			$stmt->execute(['client_id' => $client_id]);
			$result = $stmt->fetchColumn();
			$stmt->closeCursor();

			return $result;
		}

		public function getEncryptionAlgorithm($client_id = null)
		{
			/*
			$stmt = $this->getPdo()->prepare($sql = sprintf('SELECT encryption_algorithm FROM %s WHERE client_id=:client_id OR client_id IS NULL ORDER BY client_id IS NOT NULL DESC', $this->config['public_key_table']));

			$stmt->execute(compact('client_id'));
			if ($result = $stmt->fetch(\PDO::FETCH_ASSOC)) {
				return $result['encryption_algorithm'];
			}
			*/
			return 'RS256';
		}

		/**
		 * DDL to create OAuth2 database and tables for PDO storage
		 *
		 * @see https://github.com/dsquier/oauth2-server-php-mysql
		 */
		public function getBuildSql($dbName = 'oauth2_server_php')
		{
			$sql = "
			CREATE TABLE {$this->config['client_table']} (
			  client_id			 VARCHAR(80)   NOT NULL,
			  client_secret		 VARCHAR(80)   NOT NULL,
			  redirect_uri		  VARCHAR(2000),
			  grant_types		   VARCHAR(80),
			  scope				 VARCHAR(4000),
			  user_id			   VARCHAR(80),
			  PRIMARY KEY (client_id)
			);

			CREATE TABLE {$this->config['access_token_table']} (
			  access_token		 VARCHAR(40)	NOT NULL,
			  client_id			VARCHAR(80)	NOT NULL,
			  user_id			  VARCHAR(80),
			  expires			  TIMESTAMP	  NOT NULL,
			  scope				VARCHAR(4000),
			  PRIMARY KEY (access_token)
			);

			CREATE TABLE {$this->config['code_table']} (
			  authorization_code  VARCHAR(40)	NOT NULL,
			  client_id		   VARCHAR(80)	NOT NULL,
			  user_id			 VARCHAR(80),
			  redirect_uri		VARCHAR(2000),
			  expires			 TIMESTAMP	  NOT NULL,
			  scope			   VARCHAR(4000),
			  id_token			VARCHAR(1000),
			  PRIMARY KEY (authorization_code)
			);

			CREATE TABLE {$this->config['refresh_token_table']} (
			  refresh_token	   VARCHAR(40)	NOT NULL,
			  client_id		   VARCHAR(80)	NOT NULL,
			  user_id			 VARCHAR(80),
			  expires			 TIMESTAMP	  NOT NULL,
			  scope			   VARCHAR(4000),
			  PRIMARY KEY (refresh_token)
			);

			CREATE TABLE {$this->config['user_table']} (
			  username			VARCHAR(80),
			  password			VARCHAR(80),
			  first_name		  VARCHAR(80),
			  last_name		   VARCHAR(80),
			  email			   VARCHAR(80),
			  email_verified	  BOOLEAN,
			  scope			   VARCHAR(4000)
			);

			CREATE TABLE {$this->config['scope_table']} (
			  scope			   VARCHAR(80)  NOT NULL,
			  is_default		  BOOLEAN,
			  PRIMARY KEY (scope)
			);

			CREATE TABLE {$this->config['jwt_table']} (
			  client_id		   VARCHAR(80)   NOT NULL,
			  subject			 VARCHAR(80),
			  public_key		  VARCHAR(2000) NOT NULL
			);

			CREATE TABLE {$this->config['jti_table']} (
			  issuer			  VARCHAR(80)   NOT NULL,
			  subject			 VARCHAR(80),
			  audiance			VARCHAR(80),
			  expires			 TIMESTAMP	 NOT NULL,
			  jti				 VARCHAR(2000) NOT NULL
			);

			CREATE TABLE {$this->config['public_key_table']} (
			  client_id			VARCHAR(80),
			  public_key		   VARCHAR(2000),
			  private_key		  VARCHAR(2000),
			  encryption_algorithm VARCHAR(100) DEFAULT 'RS256'
			)
	";

			return $sql;
		}
	}
?>
