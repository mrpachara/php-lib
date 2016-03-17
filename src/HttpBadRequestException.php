<?php
	namespace sys;

	class HttpBadRequestException extends HttpException {
		function __construct($previous = null){
			parent::__construct('Bad Request', 400, $previous);
		}
	}

?>
