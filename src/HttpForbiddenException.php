<?php
	namespace sys;

	class HttpForbiddenException extends HttpException {
		function __construct($message = 'Forbiddent', $previous = null){
			parent::__construct($message, 403, $previous);
		}
	}
?>
