<?php
	namespace sys;

	class HttpInternalServerErrorException extends HttpException {
		function __construct($previous = null){
			parent::__construct('Internal Server Error', 500, $previous);
		}
	}
?>
