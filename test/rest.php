<?php
	require_once '../vendor/autoload.php';

	require_once 'infra.inc.php';

	require_once 'oauth2-server-config.php';

	define('REST', true);

	ini_set('display_errors', 0);

	header("Access-Control-Allow-Origin: *");

	$GLOBALS['_grantservice'] = new \sys\GrantService($infra['authz'], $GLOBALS['_oauth2server']);

	function rest_error($errno, $errstr, $errfile, $errline){
		\sys\Rest::response([
			'errors' => [
				new \Exception($errfile.':'.$errline.':'.$errstr, $errno),
			],
		], null, 500, "Internal Server Error");
	}

	function shutdown_handler(){
		if(($error = error_get_last()) && (($error['type'] & (E_ERROR | E_WARNING | E_PARSE | E_NOTICE)) > 0)){
			rest_error(-1, $error['message'], $error['file'], $error['line']);
		}
		//if($error) echo $error['type'];
	}

	set_error_handler('rest_error');

	register_shutdown_function('shutdown_handler');

	$GLOBALS['_rest'] = new \sys\Rest();

	//if($GLOBALS['_rest']->getService() != 'view') sleep(1);
	//sleep(1);

	if(($GLOBALS['_rest']->getModule() != null) && (file_exists($infra['rest']['path'].$GLOBALS['_rest']->getModule()."/index.php"))){
		require_once $infra['rest']['path'].$GLOBALS['_rest']->getModule()."/index.php";
	} else{
		$GLOBALS['_rest']->response([
			'errors' => [
				new \Exception('module: '.$GLOBALS['_rest']->getModule()." not found", 404),
			],
		], null, 404, 'Not Found');
	}
?>
