<?php
	require_once '../vendor/autoload.php';
	require_once 'infra.inc.php';

	header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
	header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past

	header("Content-Type: text/plain; charset=UTF-8");

	if(empty($_POST['code']) || empty($_POST['name']) || empty($_POST['data'])) exit("invalid data!!!");

	$pdoconfigurated = new \sys\PDOConfigurated($infra['db']);

	$pdo = $pdoconfigurated->getInstance();

	$pdo->beginTransaction();

	try{
		$stmt = $pdo->prepare("
			INSERT INTO stub.data01 (
				  code
				, name
				, data
			) VALUES (
				  :code
				, :name
				, :data
			)
		");

		$stmt->execute([
			'code' => $_POST['code'],
			'name' => $_POST['name'],
			'data' => $_POST['data'],
		]);
		if($stmt->rowCount() == 0) echo "No data to be updated!!!".PHP_EOL;
		$stmt->closeCursor();
	} catch(\PDOException $excp){
		$pdo->rollBack();
		exit($excp->getMessage().PHP_EOL);
	}

	if($pdo->commit()){
		echo "add success".PHP_EOL;
	} else{
		echo "add fail!!!".PHP_EOL;
	}
?>
