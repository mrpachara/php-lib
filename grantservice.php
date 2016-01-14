<?php
	namespace sys;

	class RestTokenException extends HttpException {
		function __construct($message, $code = null, $previous = null){
			parent::__construct($message, $code, $previous);
		}
	}

	class GrantService{
		private $conf;
		private $oauth2server;

		private $request;
		private $response;

		private $accessTokenData = null;

		function __construct($conf, $oauth2server){
			$this->conf = $conf;
			$this->oauth2server = $oauth2server;

			$this->request = \OAuth2\Request::createFromGlobals();
			$this->response = new \OAuth2\Response();

			//$this->accessTokenData = $oauth2server->getAccessTokenData($this->request);
		}

		public function getAccessTokenData(){
			/*
			if(empty($this->accessTokenData)){
				$accessTokenData = $this->oauth2server->getAccessTokenData($this->request);
				if(!empty($accessTokenData)) $this->accessTokenData = $accessTokenData;
			}

			return $this->accessTokenData;
			*/

			$accessTokenData =$this->oauth2server->getAccessTokenData($this->request);
			return (empty($accessTokenData))? null : $accessTokenData;
		}

		public function getAccessToken(){
			$providedToken = $this->oauth2server->getTokenType()->getAccessTokenParameter($this->request, $this->response);
			return ($this->getAccessTokenData() === null)? null :  $providedToken;
		}

		public function getScopes(){
			if($this->getAccessTokenData() === null) return null;

			$scope = trim($this->getAccessTokenData()['scope']);

			$scopes = ((empty($scope))? [] : explode(' ', $scope));

			if(!empty($this->conf['default'])) $scopes = array_merge($scopes, (array)$this->conf['default']);

			$scopes = array_unique($scopes);

			return $scopes;
		}

		public function authoz(){
			$accepted_scopes = func_get_args();

			$compared_scopes = array_merge($accepted_scopes, (!empty($this->conf['superuserrole']))? (array)$this->conf['superuserrole'] : []);

			$compared_scopes = array_unique($compared_scopes);

			$scopes = $this->getScopes();

			if($scopes === null) return false;
			if(empty($accepted_scopes)) return true;

			$tmpintersect = array_intersect($compared_scopes, $scopes);

			return !empty($tmpintersect);
		}

		public function authozExcp(){
			if(!call_user_func_array(array($this, 'authoz'), func_get_args())){
				$this->oauth2server->verifyResourceRequest($this->request, $this->response, 'not_allowed');
				$this->response->send();
				exit();
			}

			return true;
		}

		public function getUsername(){
			return ($this->getAccessTokenData() !== null)? $this->getAccessTokenData()['user_id'] : null;
		}

		public function isSuperUser(){
			return (!empty($this->conf['superuserrole']) && $this->authoz($this->conf['superuserrole']));
		}

		public function isUser($id){
			return (($this->getAccessTokenData() !== null) && ($id == $this->getAccessTokenData()['user_id']));
		}

		public function getAllowedGrants(){
			$allroles = array_keys($this->conf['rolenames']);

			return array_values(array_diff(
				  $allroles
				, ($this->isSuperUser())? array() : (array)$this->conf['specialroles']
			));
		}
	}
?>
