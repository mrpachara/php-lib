<?php
	require_once '../vendor/autoload.php';
	require_once 'infra.inc.php';

	require_once 'oauth2-server-config.php';

	$GLOBALS['_tokenpdoconfigurated']->getInstance()->beginTransaction();

	try{
		$GLOBALS['_oauth2server']->handleTokenRequest(OAuth2\Request::createFromGlobals())->send();
	} catch(\Exception $excp){
		$GLOBALS['_tokenpdoconfigurated']->getInstance()->rollBack();

		header(((isset($_SERVER['SERVER_PROTOCOL']))? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.1').' 500 Internal Sever Error');
		header('Content-Type: application/json; charset=UTF-8');
		header('Cache-Control: no-store');
		header('Pragma: no-cache');

		$error = [
			'error' => 'internal_server_error',
			'error_description' => $excp->getMessage(),
		];

		if($infra['debug']['level'] > 0) $error += [
			'error_file' => $excp->getFile().':'.$excp->getLine(),
		];

		if($infra['debug']['level'] > 1) $error += [
			'error_trace' => $excp->getTraceAsString(),
		];

		if($infra['debug']['level'] > 2) $error += [
			'error_traces' => $excp->getTrace(),
		];

		exit(json_encode($error));
	}

	$GLOBALS['_tokenpdoconfigurated']->getInstance()->commit();
?>
