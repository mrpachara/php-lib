<?php
	require_once '../vendor/autoload.php';

	$config = new \sys\Config('configuration.json.php');

	print_r($config->links('mytest'));

	echo "Successful!!!";
?>
