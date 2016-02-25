<html>
	<head>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<title>Add Stub Data</title>
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

#cp-form label {
	display: flex;
	flex-direction: row;
}

.flex-row {
	display: flex !important;
	flex-direction: row !important;
}

.flex-column {
	display: flex !important;
	flex-direction: column !important;
}
		</style>
	</head>
	<body>
		<form id="cp-form" action="add-stub-data_submit.php" method="post">
			<label>
				<span>Code</span>
				<input name="code" class="cl-flex" />
			</label>
			<label>
				<span>Name</span>
				<input name="name" class="cl-flex" />
			</label>
			<label class="flex-column">
				<span>Data</span>
				<span class="cl-flex"></span>
				<textarea name="data" cols="80" rows="10"></textarea>
			</label>
			<div style="text-align: center;">
				<button type="submit">Add</button>
			</div>
		</form>
	</body>
</html>
