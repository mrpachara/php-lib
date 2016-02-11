<?php
	//OAuth2\Autoloader::register();

	$GLOBALS['_tokenpdoconfigurated'] = new sys\PDOConfigurated($infra['db']);

	// $dsn is the Data Source Name for your database, for exmaple "mysql:dbname=my_oauth2_db;host=localhost"
	$GLOBALS['_oauth2storage'] =  new sys\oauth2\storage\PDO($infra['authz'], $GLOBALS['_tokenpdoconfigurated']);

	// Pass a storage object or array of storage objects to the OAuth2 server class
	$GLOBALS['_oauth2server'] = new OAuth2\Server($GLOBALS['_oauth2storage'], ['store_encrypted_token_string' => false]);
	//$GLOBALS['_oauth2server'] = new sys\oauth2\Server($GLOBALS['_oauth2storage'], ['store_encrypted_token_string' => false]);
	// Add the "Client Credentials" grant type (it is the simplest of the grant types)
	$GLOBALS['_oauth2server']->addGrantType(new OAuth2\GrantType\ClientCredentials($GLOBALS['_oauth2storage']));

	// Add the "Authorization Code" grant type (this is where the oauth magic happens)
	$GLOBALS['_oauth2server']->addGrantType(new OAuth2\GrantType\AuthorizationCode($GLOBALS['_oauth2storage']));

	// Add the "User Credentials" grant type (this is where the oauth magic happens)
	$GLOBALS['_oauth2server']->addGrantType(new OAuth2\GrantType\UserCredentials($GLOBALS['_oauth2storage']));

	// Add the "Refresh Token" grant type (this is where the oauth magic happens)
	$GLOBALS['_oauth2server']->addGrantType(new OAuth2\GrantType\RefreshToken($GLOBALS['_oauth2storage'], [
		'always_issue_new_refresh_token' => true,
		//'unset_refresh_token_after_use' => true, // unset_refresh_token_after_use is automaticaly true if always_issue_new_refresh_token is true
	]));
?>
