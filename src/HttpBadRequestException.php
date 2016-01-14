<?php
	namespace sys;

	class HttpBadRequestException extends HttpException {
		function __construct($message = 'Bad Request', $previous = null){
			parent::__construct($message, 400, $previous);
		}
	}

?>
