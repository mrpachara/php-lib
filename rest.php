<?php
	namespace sys;

	class HttpException extends \Exception {
		function __construct($message, $code = 500, $previous = null){
			parent::__construct($message, $code, $previous);
		}
	}

	class HttpBadRequestException extends HttpException {
		function __construct($message = 'Bad Request', $previous = null){
			parent::__construct($message, 400, $previous);
		}
	}

	class HttpForbiddenException extends HttpException {
		function __construct($message = 'Forbiddent', $previous = null){
			parent::__construct($message, 403, $previous);
		}
	}

	class HttpNotFoundException extends HttpException {
		function __construct($message = 'Not Found', $previous = null){
			parent::__construct($message, 404, $previous);
		}
	}

	class HttpMethodNotAllowedException extends HttpException {
		function __construct($message = 'Method Not Allowed', $previous = null){
			parent::__construct($message, 405, $previous);
		}
	}

	class HttpInternalServerErrorException extends HttpException {
		function __construct($message = 'Internal Server Error', $previous = null){
			parent::__construct($message, 500, $previous);
		}
	}

	class HttpNotImplementedException extends HttpException {
		function __construct($message = 'Not Implemented', $previous = null){
			parent::__construct($message, 501, $previous);
		}
	}

	class Rest {
		const CONTENTYPE_TYPE_PROPERTY_NAME = 'Content-Type';
		const DEFAULT_RESPONSE_MESSAGE = 'NONE';
		const SORT_SIGNIFICANT = 1000;

		const CONFIG_SERVICE = 'configuration';

		public static function getReference(){
			$protocol = (empty($_SERVER['HTTPS']) || ($_SERVER['HTTPS'] == 'off'))? 'http' : 'https';
			$host = (isset($_SERVER['HTTP_HOST']))? $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME'];
			$port = '';
			if($protocol == 'http'){
				if($_SERVER['SERVER_PORT'] != 80) $port = ':'.$_SERVER['SERVER_PORT'];
			} else{
				if($_SERVER['SERVER_PORT'] != 443) $port = ':'.$_SERVER['SERVER_PORT'];
			}

			return $protocol.'://'.$host.$port;
		}

		public static function parseContentType($str){
			$contentType = array(
				 'origin' => $str
				,'type' => null
				,'params' => null
			);

			if(!empty($str)){
				$contentTypes = explode(';', strtolower(str_replace(' ', '', $str)), 2);
				$contentType['type'] = $contentTypes[0];
				if(isset($contentTypes[1])){
					$contentType['params'] = explode(';', $contentTypes[1]);
				}

				if(empty($contentType['type'])) $contentType['type'] = null;
				if(empty($contentType['params'])) $contentType['params'] = null;
			}

			return $contentType;
		}

		public static function getResponseContentType(){
			$responseContentTypes = headers_list();
			$propName = strtolower(static::CONTENTYPE_TYPE_PROPERTY_NAME);
			$propLength = strlen($propName);

			foreach($responseContentTypes as $responseContentType){
				if(substr($responseContentType, 0, $propLength) == $propName){
					return $responseContentType;
				}
			}

			return null;
		}

		protected static function startsWith($haystack, $needle){
				// search backwards starting from haystack length characters from the end
				return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== FALSE;
		}
		protected static function endsWith($haystack, $needle){
				// search forward starting from end minus needle length characters
				return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== FALSE);
		}

		public static function getResponseNegotiation(){
			$acceptList = array();
			$sortKey = array();

			if(isset($_SERVER['HTTP_ACCEPT'])){
				$httpAccepts = explode(',', strtolower(str_replace(' ', '', $_SERVER['HTTP_ACCEPT'])));

				$significant = 0;
				foreach($httpAccepts as $httpAccept){
					$q = 1;
					$contentType = static::parseContentType($httpAccept);

					foreach((array)$contentType['params'] as $param){
						$param_splits = explode('=', $param, 2);

						if(($param_splits[0] == 'q') && isset($param_splits[1])){
							$q = (float)$param_splits[1];
						}
					}

					$acceptList[] = $contentType;
					$sortKey[] = ((int)($q * static::SORT_SIGNIFICANT)) - ($significant++);
				}
			}

			array_multisort($sortKey, SORT_DESC, $acceptList);

			return $acceptList;
		}

		public static function isMatchContentType($targetContentType, $patternContentType){
			if(empty($targetContentType['type']) || empty($patternContentType['type'])) return false;

			$isMatch = false;

			$tTypes = explode('/', $targetContentType['type']);
			$pTypes = explode('/', $patternContentType['type']);

			if(($pTypes[0] == '*') || ($pTypes[0] == $tTypes[0])){
				$tSubTypes = explode('+', $tTypes[1]);
				$pSubTypes = explode('+', $pTypes[1]);

				$tSubTypes_length = count($tSubTypes);
				$pSubTypes_length = count($pSubTypes);

				for($i = 0; $i < $pSubTypes_length; $i++){
					if(!isset($tSubTypes[$i])){
						$isMatch = ($pSubTypes[$i] == '*');
						break;
					}
					if(!(($pSubTypes[$i] == '*') || ($pSubTypes[$i] == $tSubTypes[$i]))){
						$isMatch = false;
						break;
					}
				}
				if(($i == $tSubTypes_length) && ($i == $pSubTypes_length)) $isMatch = true;
			}

			return $isMatch;
		}

		public static function getBestContentType($contentTypesStrs, $forceType = null){
			$bestContentType = null;

			$negotiationContentTypes = static::getResponseNegotiation();
			if(!empty($contentTypesStrs)){
				$contentTypes = array();
				foreach((array)$contentTypesStrs as $contentTypesStr){
					$contentTypes[] = static::parseContentType($contentTypesStr);
				}

				if(count($negotiationContentTypes) == 0) $bestContentType = $contentTypes[0]['origin'];
				foreach($negotiationContentTypes as $negotiationContentType){
					foreach($contentTypes as $contentType){
						if(static::isMatchContentType($contentType, $negotiationContentType)){
							$bestContentType = $contentType['origin'];
							break;
						}
					}

					if($bestContentType !== null) break;
				}
			}

			if(($bestContentType === null) && ($forceType !== null)){
				if(is_bool($forceType) && $forceType && (count($negotiationContentTypes) > 0)){
					$bestContentType = $negotiationContentTypes[0]['origin'];
				} else if(is_string($forceType) && !empty($forceType)){
					$bestContentType = $forceType;
				}
			}

			return $bestContentType;
		}

		public static function getContentTemplate($template, $model){
			ob_start();
			include $template;
			return ob_get_clean();
		}

		public static function response($data, $forceType = null, $code = null, $message = null){
			$response = null;
			$exit_code = null;
			$exit_response = null;

			$responseContentType = static::getResponseContentType();
			if(empty($responseContentType)){
				if(!empty($data['errors'])){
					$exit_code = 500;
					$exit_message = 'Internal Server Error';

					/* prepare error data */
					foreach($data['errors'] as &$error){
						if($error instanceof HttpException){
							$exit_code = $error->getCode();
							$exit_message = strtok($error->getMessage(), "\n");
						/*
						} else if(!empty($error['code-response'])){
							$exit_code = $error['code-response'];
							$exit_message = (!empty($error['message-response']))? $error['message-response'] : static::DEFAULT_RESPONSE_MESSAGE;

							if(empty($error['code'])){
								$error['code'] = $exit_code;
								$error['message'] = $exit_message;
							}

							unset($error['code-response']);
							unset($error['message-response']);
						*/
						}

						$error = array(
							 'code' => $error->getCode()
							,'message' => $error->getMessage()
							,'exception' => $error->__toString()
						);

						// TODO: less info for debug == 0
						/*
						if($conf['debug'] == 0){
							if($error['exception'] instanceof \PDOException) $message = "Database Error!!!";

							unset($error['exception']);
						};
						*/
					}
				}

				$bestContentTypeStr = $forceType;
				if($bestContentTypeStr === null){
					$bestContentTypeStr = static::getBestContentType(array(
						 'application/json; charset=utf-8'
						,'application/x-www-form-urlencoded'
						,'application/xhtml+xml; charset=utf-8'
						,'text/html; charset=utf-8'
					), true);
				}

				header(static::CONTENTYPE_TYPE_PROPERTY_NAME.": {$bestContentTypeStr}");
				$bestContentType = static::parseContentType($bestContentTypeStr);

				if(is_resource($data)){
					$response = $data;
				} else{
					switch($bestContentType['type']){
						case 'application/json':
							$response = json_encode($data);
							break;
						case 'application/x-www-form-urlencoded':
							$response = http_build_query($data);
							break;
						default:
							@$response = (is_string($data))? $data : var_export($data, true);
					}
				}
			} else{
				$response = $data;
			}

			if($code !== null){
				$exit_code = $code;
				$exit_message = ($message !== null)? $message : static::DEFAULT_RESPONSE_MESSAGE;
			}

			if($exit_code !== null){
				header(((isset($_SERVER['SERVER_PROTOCOL']))? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0')." {$exit_code} {$exit_message}");
			}

			if(is_resource($response) && ((get_resource_type($response) === 'file') || (get_resource_type($response) === 'stream'))){
				fpassthru($response);
				exit();
			} else{
				exit($response);
			}
		}

		private $restPath = null;
		private $method = null;

		private $query = array();
		private $fragment = null;

		private $arguments = array();

		private $module = null;
		private $service = null;

		private $responseContentType = null;

		private $contentType = array(
			 'origin' => null
			,'type' => null
			,'params' => null
		);

		private $content = null;

		function __construct($absolute = true, $debug = 0){
			$this->method = $_SERVER['REQUEST_METHOD'];

			$urls = parse_url($_SERVER['REQUEST_URI']);
			if(!empty($urls['query'])) parse_str($urls['query'], $this->query);

			$this->restPath = (($absolute)? static::getReference() : '').substr($urls['path'], 0, strlen($urls['path']) - strlen($_SERVER['PATH_INFO'])).'/';
			if(!empty($_SERVER['PATH_INFO'])){
				$pathinfos = explode('/', substr($_SERVER['PATH_INFO'], 1));
				if(($pathinfos[0] != '.') && ($pathinfos[0] != '..')) $this->module = $pathinfos[0];
				if(isset($pathinfos[1]) && ($pathinfos[1] != '.') && ($pathinfos[1] != '..')) $this->service = $pathinfos[1];
				$this->arguments = array_slice($pathinfos, 2);
			}

			if(isset($_SERVER['CONTENT_TYPE'])){
				$this->contentType = static::parseContentType($_SERVER['CONTENT_TYPE']);
			}

			/*
			$content = file_get_contents("php://input");
			if(!empty($content)){
				switch($this->contentType['type']){
					case 'application/x-www-form-urlencoded':
						parse_str($content, $this->content);
						break;
					case 'application/json':
						$this->content = json_decode($content, true);
						break;
					default:
						$this->content = json_decode($content, true);
						if(!empty($this->content)) break;
						parse_str($content, $this->content);
						if(!empty($this->content)) break;
						$this->content = $content;
				}
			}
			*/
			if(is_array($this->contentType)){
				switch($this->contentType['type']){
					case 'application/x-www-form-urlencoded':
						$content = file_get_contents("php://input");
						parse_str($content, $this->content);
						break;
					case 'application/json':
						$content = file_get_contents("php://input");
						$this->content = json_decode($content, true);
						break;
					default:
						/* unknow content type get as stream */
						$this->content = fopen("php://input", "r");
				}
			}
		}

		public function getRestPath($path = ''){
			return $this->restPath.$path;
		}

		public function getModulePath($path = ''){
			return $this->getRestPath($this->module.(($path === '')? '' : '/')).$path;
		}

		public function getServicePath($path = ''){
			return $this->getModulePath($this->service.(($path === '')? '' : '/')).$path;
		}

		public function getRestUri(){
			return $this->getModulePath($this->service.((empty($this->arguments))? '' : '/'.implode('/', $this->arguments)));
		}

		public function getConfigUri($module = null){
			return $this->getRestPath((($module === null)? $this->module : $module).'/'.self::CONFIG_SERVICE);
		}

		public function getMethod(){
			return $this->method;
		}

		public function getQuery($name = null){
			if($name === null){
				return $this->query;
			} else{
				return (empty($this->query[$name]))? null : $this->query[$name];
			}
		}

		public function getFragment(){
			return $this->fragment;
		}

		public function getArguments(){
			return $this->arguments;
		}

		public function getArgument($index){
			return (isset($this->arguments[$index]))? $this->arguments[$index] : null;
		}

		public function bind($names = null){
			$map = array();

			if(!empty($names)){
				$names = (array)$names;
				$length = count($names);

				for($i = 0; ($i < $length) && isset($this->arguments[$i]); $i++){
					$map[$names[$i]] = $this->arguments[$i];
				}
			}

			return $map;
		}

		public function getModule(){
			return $this->module;
		}

		public function getService(){
			return $this->service;
		}

		public function isService($services){
			$services = (array)$services;

			return in_array($this->service, $services);
		}

		public function getContentType(){
			return $this->contentType;
		}

		public function getContent(){
			return $this->content;
		}

		public function setCacheLimit($limit){
			header('Expires: Thu, 19 Nov 1981 08:52:00 GMT');
			header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
			header('Pragma: no-cache');
		}

		public function setResponseContentType($responseContentType){
			$this->responseContentType = $responseContentType;
		}

		public function sendResponse($data, $forceType = null){
			$this->response($data, ($forceType === null)? $this->responseContentType : $forceType);
		}
	}
?>
