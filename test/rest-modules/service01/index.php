<?php
	if(!defined("REST")){
		header(((isset($_SERVER['SERVER_PROTOCOL']))? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0')." 404 Not Found");
		exit;
	}

	if(!defined('RESTCONFIGURATED')) define('RESTCONFIGURATED', true);

	$service = $GLOBALS['_rest']->getService();
	$config = new \sys\Config(__DIR__.'/'.\sys\Rest::CONFIG_SERVICE.'.php');
	$GLOBALS['_pdoconfigurated'] = new \sys\Pdoconfigurated($infra['db']);

	try{
		if($service == \sys\Rest::CONFIG_SERVICE){
			include $service.'.php';
		}else if(is_dir(__DIR__.'/resources/'.$service)){
			if(file_exists(__DIR__.'/resources/'.$service.'/'.'index.php')){
				require_once __DIR__.'/resources/'.$service.'/'.'index.php';
			} else if(file_exists(__DIR__.'/resources/'.$service.'/'.$GLOBALS['_rest']->getArgument(0))){
				$filename = __DIR__.'/resources/'.$service.'/'.$GLOBALS['_rest']->getArgument(0);
				$data = fopen($filename, 'rb');
				$GLOBALS['_rest']->setResponseContentType(mime_content_type($filename));
			} else{
				throw new \sys\HttpNotFoundException();
			}
		} else{
			$GLOBALS['_rest']->setCacheLimit('nocache');
			//$GLOBALS['_grantservice']->authozExcp();

			require_once 'service/index.php';
			include $service.'.php';
		}
	} catch(\sys\DataServiceException $excp){
		switch($excp->getCode()){
			case \sys\DataServiceException::NOT_FOUND:
				$excp = new \sys\HttpNotFoundException($excp);
				break;
			case \sys\DataServiceException::CANNOT_PROCESS:
				$excp = new \sys\HttpMethodNotAllowedException($excp);
				break;
		}

		throw $excp;
	}

	$GLOBALS['_rest']->sendResponse($data);
?>
