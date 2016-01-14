<?php
	namespace sys;

	class HttpNotFoundException extends HttpException {
		function __construct($message = 'Not Found', $previous = null){
			parent::__construct($message, 404, $previous);
		}
	}

?>
