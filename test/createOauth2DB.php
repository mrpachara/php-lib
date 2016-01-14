<?php
	header("Content-Type: text/plain; charset=utf8");

	require_once '../vendor/autoload.php';

	$pdoconfigurated = new sys\PdoConfigurated([
		'dns' => 'mysql:host=localhost;dbname=tester_phplib',
		'username' => 'tester',
		'password' => '1234',
		'options' => [
			\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
			\PDO::ATTR_EMULATE_PREPARES => false,
			\PDO::ATTR_AUTOCOMMIT => false,
			\PDO::ATTR_ORACLE_NULLS => \PDO::NULL_EMPTY_STRING,

			// for MySQL
			\PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
			\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8, time_zone = '+00:00'",
		],
	]);

	$pdostorage = new sys\oauth2\storage\PDO([
		'default' => 'ALL',
		'superusername' => 'root',
		'superuserrole' => 'ROOT',
		'forbidden_code' => 403,
		'forbidden_message' => 'Forbidden',
		'rolenames' => [
			'ADMIN' => 'Administrator',
			'MANAGER' => 'Manager',
			'OFFICE' => 'Office Staff',
			'ACCOUNT' => 'Account',
			'PURCHASING' => 'Purchasing',
			'STORE' => 'Store',
			'STAFF' => 'Staff',
			'USER' => 'User',
		],
		'specialroles' => ["ADMIN"],
	], $pdoconfigurated);

	echo $pdostorage->getBuildSql();
?>
