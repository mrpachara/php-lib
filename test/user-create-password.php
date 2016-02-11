<html>
	<head>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<title>User Create Password</title>
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
		</style>
	</head>
	<body>
		<form id="cp-form" action="user-create-password_submit.php" method="post">
			<label>
				<span>Username</span>
				<input type="text" name="username" />
			</label>
			<label>
				<span>Password</span>
				<input type="text" name="password" />
			</label>
			<button type="submit">Update</button>
		</form>
	</body>
</html>
