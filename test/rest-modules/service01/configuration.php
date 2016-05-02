<?php
	if(!defined("RESTCONFIGURATED")){
		header(((isset($_SERVER['SERVER_PROTOCOL']))? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0')." 404 Not Found");
		exit;
	}

	$data = [
		'uri' => $GLOBALS['_rest']->getConfigUri(),
		'new-method' => '::new',
	];

	$data['links'] = [
		['rel' => 'service', 'href' => $GLOBALS['_rest']->getModulePath("data01"), 'alias' => "data01"],
		['rel' => 'service', 'href' => $GLOBALS['_rest']->getModulePath("data01/{$data['new-method']}"), 'alias' => "data01-new"],
		['rel' => 'service', 'href' => $GLOBALS['_rest']->getModulePath("data01/{{ id }}"), 'alias' => "data01-item"],
		['rel' => 'resource', 'href' => $GLOBALS['_rest']->getModulePath("data01-domain"), 'alias' => "data01-domain"],
	];
?>
