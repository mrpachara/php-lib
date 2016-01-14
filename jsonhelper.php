<?php
	function json_exit($json){
		global $conf;

		if(!empty($json['errors'])){
			$code = 500;
			$message = "Internal Server Error";
			foreach($json['errors'] as &$error){
				if(!empty($error['exception']) && ($error['exception'] instanceof Exception)){
					if(empty($error['code'])) $error['code'] = $error['exception']->getCode();
					if(empty($error['message'])) $error['message'] = $error['exception']->getMessage();

					if($conf['debug'] == 0){
						if($error['exception'] instanceof PDOException) $message = "Database Error!!!";

						unset($error['exception']);
					};
				} else{
					if(empty($error['code'])) $error['code'] = $code;
					if(empty($error['message'])) $error['message'] = $message;
				}

				$code = ($error['code'] <= 505)? $error['code'] : 500;
				$message = strtok($error['message'], "\n");
			}

			header(((isset($_SERVER['SERVER_PROTOCOL']))? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0')." {$code} {$message}");
		}

		header("Content-Type: application/json; charset=utf-8");

		exit(json_encode($json));
	}

	function json_content(){
		return json_decode(file_get_contents("php://input"), true);
	}
?>
