<?php
	if(!defined("RESTCONFIGURATED")){
		header(((isset($_SERVER['SERVER_PROTOCOL']))? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0')." 404 Not Found");
		exit;
	}

	$data = [
		'uri' => $GLOBALS['_rest']->getRestUri(),
	];

	$reqParams = $GLOBALS['_rest']->bind(['id']);

/*
=================================================
	Call directly to service
=================================================
*/
	if($reqParams['id'] === null){
		if($GLOBALS['_rest']->isMethod(['GET', 'POST'])){
			$options = ($GLOBALS['_rest']->isMethod('GET'))?
				$GLOBALS['_rest']->getQuery() : $GLOBALS['_rest']->getContent();

			$data['self'] = $_service->getAll($options);
		} else{
			throw new \sys\HttpMethodNotAllowedException();
		}
/*
=================================================
	Call with item id
=================================================
*/
	} else{
		if($GLOBALS['_rest']->isMethod('GET')){
			$data['self'] = $_service->get($reqParams['id']);
		} else{
			throw new \sys\HttpMethodNotAllowedException();
		}
	}
?>
