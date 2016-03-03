<?php
	require_once '../vendor/autoload.php';
	require_once 'infra.inc.php';

	header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
	header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past

	//header("Content-Type: text/plain; charset=UTF-8");

	$pdoconfigurated = new \sys\PDOConfigurated($infra['db']);

	$pdo = $pdoconfigurated->getInstance();

	$pdo->beginTransaction();

	$stmt = $pdo->prepare("
		SELECT
			  account.*
			, sys_account.*
			, (CASE
				WHEN sys_user.id IS NOT NULL THEN 'user'
				WHEN sys_group.id IS NOT NULL THEN 'group'
				WHEN sys_client.id IS NOT NULL THEN 'client'
				ELSE 'unknown'
			END) AS type
		FROM sys.account AS sys_account
			INNER JOIN account AS account ON (sys_account.id_account = account.id)
			LEFT JOIN sys.user AS sys_user ON (sys_account.id = sys_user.id_sys_account)
			LEFT JOIN sys.group AS sys_group ON (sys_account.id = sys_group.id_sys_account)
			LEFT JOIN sys.client AS sys_client ON (sys_account.id = sys_client.id_sys_account)
	");

	$stmt->execute();
	$result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
	$stmt->closeCursor();

	$stmt_groups = $pdo->prepare("
		SELECT
			  account.code
		FROM sys.groupaccount AS sys_groupaccount
			INNER JOIN sys.group AS sys_group ON (sys_groupaccount.id_sys_group = sys_group.id)
			INNER JOIN sys.account AS sys_account ON (sys_group.id_sys_account = sys_account.id)
			INNER JOIN account AS account ON (sys_account.id_account = account.id)
		WHERE sys_groupaccount.id_sys_account = :id_sys_account
	");
	$stmt_roles = $pdo->prepare("
		SELECT
			  sys_accountrole.role
		FROM account AS account
			INNER JOIN sys.account AS sys_account ON (account.id = sys_account.id_account)
			INNER JOIN sys.accountrole AS sys_accountrole ON (sys_account.id = sys_accountrole.id_sys_account)
		WHERE sys_accountrole.id_sys_account = :id_sys_account
	");

	$sysaccounts = [];
	foreach($result as $sysaccount){
		$stmt_groups->execute(['id_sys_account' => $sysaccount['id']]);
		$group = implode(', ', $stmt_groups->fetchAll(\PDO::FETCH_COLUMN));
		$stmt_groups->closeCursor();

		$stmt_roles->execute(['id_sys_account' => $sysaccount['id']]);
		$role = implode(', ', $stmt_roles->fetchAll(\PDO::FETCH_COLUMN));
		$stmt_roles->closeCursor();

		$sysaccounts[] = array_merge($sysaccount, [
			'group' => $group,
			'role' => $role,
		]);
	}
?>
<html>
	<head>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<title>Account List</title>
		<style type="text/css">
html,
body {
	padding: 0px;
	margin: 0px;

	height: 100%;
}

body {
	font-family: sans-serif;
	font-size: 16px;
}

table {
	border-collapse: collapse;
}

table,
table>*>*>* {
	border: 1px solid black;
}

table>thead {
	background-color: rgba(128, 128, 128, 0.5);
}
		</style>
	</head>
	<body>
		<table style="width: 100%;">
			<caption>Account List</caption>
			<colgroup>
				<col style="width: 10em;" />
				<col style="width: 20em;"/>
				<col style="width: 5em;" />
				<col />
			</colgroup>
			<thead>
				<tr>
					<th>Account</th>
					<th>Fullname</th>
					<th>Type</th>
					<th>Group</th>
					<th>Role</th>
				</tr>
			</thead>
			<tbody>
<?php foreach($sysaccounts as $sysaccount): ?>
				<tr>
					<td>
						<span style="font-family: monospace;">[<span style="display: inline-block; width: 2em; text-align: right;"><?= htmlspecialchars($sysaccount['id']) ?></span>]</span>
						<span><?= htmlspecialchars($sysaccount['code']) ?></span>
					</td>
					<td><?= htmlspecialchars($sysaccount['name']) ?></td>
					<td><?= htmlspecialchars($sysaccount['type']) ?></td>
					<td><?= htmlspecialchars($sysaccount['group']) ?></td>
					<td><?= htmlspecialchars($sysaccount['role']) ?></td>
				</tr>
<?php endforeach; ?>
			</tbody>
		</table>
	</body>
</html>
