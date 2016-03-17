<?php
	namespace sys;

	class HttpNotImplementedException extends HttpException {
		function __construct($previous = null){
			parent::__construct('Not Implemented', 501, $previous);
		}
	}
?>
