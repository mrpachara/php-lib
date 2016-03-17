<?php
	require_once 'vendor/autoload.php';

	require_once 'infra.inc.php';

	require_once 'oauth2-server-config.php';

	define('REST', true);

	ini_set('display_errors', 0);

	header("Access-Control-Allow-Origin: *");

	function exception_handler($excp){
		global $infra;
		\sys\Rest::response($excp, null, null, null, $infra['debug']['level']);
	}
	set_exception_handler('exception_handler');

	function exception_error_handler($severity, $message, $file, $line) {
		if (!(error_reporting() & $severity)) {
			// This error code is not included in error_reporting
			return;
		}
		throw new \ErrorException($message, 0, $severity, $file, $line);
	}
	set_error_handler('exception_error_handler');

	function shutdown_handler(){
		global $infra;
		if(($error = error_get_last()) && (($error['type'] & (
			E_ERROR | E_PARSE | E_CORE_ERROR | E_CORE_WARNING | E_COMPILE_ERROR | E_COMPILE_WARNING | E_STRICT
		)) > 0)){
			\sys\Rest::response(
				new \ErrorException($error['message'], 0, $error['type'], $error['file'], $error['line'])
			, null, null, null, $infra['debug']['level']);
		}
	}
	register_shutdown_function('shutdown_handler');

	$GLOBALS['_grantservice'] = new \sys\GrantService($infra['authz'], $GLOBALS['_oauth2server']);

	$GLOBALS['_rest'] = new \sys\Rest(3);

	//if($GLOBALS['_rest']->getService() != 'view') sleep(1);
	//sleep(1);

	if(($GLOBALS['_rest']->getModule() != null) && (file_exists($infra['rest']['path'].$GLOBALS['_rest']->getModule()."/index.php"))){
		require_once $infra['rest']['path'].$GLOBALS['_rest']->getModule()."/index.php";
	} else{
		throw new \sys\HttpNotFoundException(
			new \Exception('module: '.$GLOBALS['_rest']->getModule()." not found", 0)
		);
	}
?>
