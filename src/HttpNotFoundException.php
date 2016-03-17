<?php
	namespace sys;

	class HttpNotFoundException extends HttpException {
		function __construct($previous = null){
			parent::__construct('Not Found', 404, $previous);
		}
	}

?>
