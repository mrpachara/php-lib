<?php
	namespace sys;

	class HttpForbiddenException extends HttpException {
		function __construct($previous = null){
			parent::__construct('Forbiddent', 403, $previous);
		}
	}
?>
