<?php
	namespace sys;

	class RestTokenException extends HttpException {
		function __construct($message, $code = null, $previous = null){
			parent::__construct($message, $code, $previous);
		}
	}
?>
