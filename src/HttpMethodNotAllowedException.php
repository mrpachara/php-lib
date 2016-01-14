<?php
	namespace sys;

	class HttpMethodNotAllowedException extends HttpException {
		function __construct($message = 'Method Not Allowed', $previous = null){
			parent::__construct($message, 405, $previous);
		}
	}
?>
