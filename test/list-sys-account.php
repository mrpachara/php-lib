<?php
	require_once '../vendor/autoload.php';
	require_once 'infra.inc.php';

	header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
	header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past

	header("Content-Type: text/plain; charset=UTF-8");

	$pdoconfigurated = new \sys\PDOConfigurated($infra['db']);

	$pdo = $pdoconfigurated->getInstance();

	$pdo->beginTransaction();

	$stmt = $pdo->prepare("
	");

	$stmt->execute();
	$stmt->closeCursor();
?>
<html>
	<head>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<title>Update User Password</title>
		<style type="text/css">
html,
body {
	padding: 0px;
	margin: 0px;

	height: 100%;
}

body {
	display: flex;
	flex-direction: column;
	flex: 1;

	align-items: center;
	justify-content: space-around;
}

#cp-form {
	display: flex;
	flex-direction: column;
}

#cp-form .cl-flex {
	flex: 1;
}

#cp-form>label {
	display: flex;
	flex-direction: row;
}
		</style>
	</head>
	<body>
		<form id="cp-form" action="update-user-password_submit.php" method="post">
			<label>
				<span>Username</span>
				<span class="cl-flex"></span>
				<input type="text" name="username" />
			</label>
			<label>
				<span>Password</span>
				<span class="cl-flex"></span>
				<input type="text" name="password" />
			</label>
			<div style="text-align: center;">
				<button type="submit">Update</button>
			</div>
		</form>
	</body>
</html>
