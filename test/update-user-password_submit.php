<?php
	require_once '../vendor/autoload.php';
	require_once 'infra.inc.php';

	header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
	header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past

	header("Content-Type: text/plain; charset=UTF-8");

	if(empty($_POST['username']) || empty($_POST['password'])) exit("invalid data!!!");

	$pdoconfigurated = new \sys\PDOConfigurated($infra['db']);

	$pdo = $pdoconfigurated->getInstance();

	$pdo->beginTransaction();

	try{
		$stmt = $pdo->prepare("
			UPDATE sys.user
			SET
				password = :password
			WHERE
				id = (
					SELECT
						sys_user.id
					FROM account
						INNER JOIN sys.account AS sys_account ON (account.id = sys_account.id_account)
						INNER JOIN sys.user AS sys_user ON (sys_account.id = sys_user.id_sys_account)
					WHERE
						account.code = :username
					LIMIT 1
				)
		");

		$stmt->execute([
			'password' => \sys\oauth2\storage\PDO::encryptPassword($_POST['password']),
			'username' => $_POST['username'],
		]);
		if($stmt->rowCount() == 0) echo "No data to be updated!!!".PHP_EOL;
		$stmt->closeCursor();
	} catch(\PDOException $excp){
		$pdo->rollBack();
		exit($excp->getMessage().PHP_EOL);
	}

	if($pdo->commit()){
		echo "Update success".PHP_EOL;
	} else{
		echo "Update fail!!!".PHP_EOL;
	}
?>
