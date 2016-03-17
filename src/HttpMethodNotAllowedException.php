<?php
	namespace sys;

	class HttpMethodNotAllowedException extends HttpException {
		function __construct($previous = null){
			parent::__construct('Method Not Allowed', 405, $previous);
		}
	}
?>
