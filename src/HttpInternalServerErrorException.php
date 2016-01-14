<?php
	namespace sys;

	class HttpInternalServerErrorException extends HttpException {
		function __construct($message = 'Internal Server Error', $previous = null){
			parent::__construct($message, 500, $previous);
		}
	}
?>
