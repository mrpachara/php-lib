<?php
	function path_getReference(){
		$protocol = $_SERVER['REQUEST_SCHEME'];
		$host = $_SERVER['SERVER_NAME'];
		$port = '';
		if($protocol == 'http'){
			if($_SERVER['SERVER_PORT'] != 80) $port = ':'.$_SERVER['SERVER_PORT'];
		} else{
			if($_SERVER['SERVER_PORT'] != 443) $port = ':'.$_SERVER['SERVER_PORT'];
		}

		return $protocol.'://'.$host.$port;
	}

	function path_getAbsUrl($path){
		$abspath = '';
		if(empty($path) || ($path[0] != '/')){
			$abspath = implode('/', explode('/', $_SERVER['SCRIPT_NAME'], -1)).'/';
		}

		return path_getReference().$abspath.((empty($path))? '' : $path);
	}

	function path_getUri(){
		return path_getReference().preg_replace('/\\.php$/', '', $_SERVER['SCRIPT_NAME']);
	}
?>
