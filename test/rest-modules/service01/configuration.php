<?php
	if(!defined("RESTCONFIGURATED")){
		header(((isset($_SERVER['SERVER_PROTOCOL']))? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0')." 404 Not Found");
		exit;
	}

	$data = [
		'uri' => $GLOBALS['_rest']->getRestUri(),
		'links' => [
			['rel' => 'service', 'href' => $GLOBALS['_rest']->getModulePath("data01"), 'alias' => "data01"],
		],
	];
?>
