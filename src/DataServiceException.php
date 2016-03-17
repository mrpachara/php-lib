<?php
	namespace sys;

	class DataServiceException extends \Exception {
		const NOT_FOUND = 1;
		const CANNOT_PROCESS = 2;
		const UNKNOWN_METHOD = 3;
	}
?>
