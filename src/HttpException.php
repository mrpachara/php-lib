<?php
	namespace sys;

	class HttpException extends \Exception {
		function __construct($message, $code = 500, $previous = null){
			parent::__construct($message, $code, $previous);
		}
	}
?>
