<?php
	if(!defined("REST")){
		header(((isset($_SERVER['SERVER_PROTOCOL']))? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0')." 404 Not Found");
		exit;
	}

	define('RESTCONFIGURATED', true);

	$service = $GLOBALS['_rest']->getService();
	$config = new \sys\Config(\sys\Rest::CONFIG_SERVICE.'.php');

	try{
		if(in_array($service, ['configuration', 'view', 'javascript', 'css', 'image', 'font'])){
			if(in_array($service, ['view', 'javascript', 'css'])){
				ob_start();
				include $service.'-resource/'.$GLOBALS['_rest']->getArgument(0).'.php';
				$data = ob_get_clean();

				$contentType = 'text/html';
				if($service == 'css'){
					$contentType = 'text/css';
				} else if($service == 'javascript'){
					$contentType = 'application/javascript';
				}

				$GLOBALS['_rest']->setResponseContentType("{$contentType}; charset=utf-8");
			} else if(in_array($service, ['image', 'font'])){
				$data = fopen(__DIR__.'/'.$service.'-resource/'.$GLOBALS['_rest']->getArgument(0), 'rb');

				$contentType = 'text/html';
				if($service == 'image'){
					$contentType = 'image/png';
				} else if($service == 'font'){
					$contentType = 'application/x-font-woff';
				}
				$GLOBALS['_rest']->setResponseContentType($contentType);
			} else{
				include $service.'.php';
			}
		} else{
			$GLOBALS['_rest']->setCacheLimit('nocache');
			$GLOBALS['_grantservice']->authozExcp();

			require_once 'service/serviceconfigurated.php';
			include $service.'.php';
		}
	} catch(Exception $excp){
		$data = [
			'uri' => $GLOBALS['_rest']->getRestUri(),
			'errors' => [
				($excp instanceof \sys\DataServiceException)?
					new \sys\HttpException(sprintf($excp->getMessage(), $GLOBALS['_rest']->getRestUri()), $excp->getCode(), $excp) : $excp,
			],
		];
	}

	$GLOBALS['_rest']->sendResponse($data);
?>
