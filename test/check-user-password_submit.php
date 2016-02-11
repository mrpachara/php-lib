<?php
	require_once '../vendor/autoload.php';
	require_once 'infra.inc.php';

	header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
	header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past

	header("Content-Type: text/plain; charset=UTF-8");

	if(empty($_POST['username']) || empty($_POST['password'])) exit("invalid data!!!");

	$pdoconfigurated = new \sys\PDOConfigurated($infra['db']);

	$pdo = $pdoconfigurated->getInstance();

	try{
		$stmt = $pdo->prepare("
			SELECT
				  account.*
				, sys_account.*
				, sys_user.*
			FROM sys.user AS sys_user
				INNER JOIN sys.account AS sys_account ON (sys_user.id_sys_account = sys_account.id)
				INNER JOIN account ON (sys_account.id_account = account.id)
			WHERE
				account.code = :username
			LIMIT 1
		");

		$stmt->execute([
			'username' => $_POST['username'],
		]);

		$user = $stmt->fetch(\PDO::FETCH_ASSOC);
		$stmt->closeCursor();
		if($user){
			$decrypt = \sys\oauth2\storage\PDO::decryptPassword($_POST['password'], $user['password']);
			if(hash_equals($user['password'], $decrypt)){
				echo "Valid password".PHP_EOL;
			} else{
				echo "Invalid password!!!".PHP_EOL;
			}
		} else{
			echo "User not found!!!".PHP_EOL;
		}
	} catch(\PDOException $excp){
		exit($excp->getMessage().PHP_EOL);
	}
?>
