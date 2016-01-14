<?php
	namespace sys;

	class HttpNotImplementedException extends HttpException {
		function __construct($message = 'Not Implemented', $previous = null){
			parent::__construct($message, 501, $previous);
		}
	}
?>
