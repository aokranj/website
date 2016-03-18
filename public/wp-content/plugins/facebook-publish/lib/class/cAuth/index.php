<?php 
	
	define( "CAUTHPATH" ,  realpath(dirname(__FILE__)));
	define( "CAUTH_THIRD_PARTY_AUTH" ,   CAUTHPATH."/Third Party" );

	require_once CAUTHPATH."/general_function.php";
	require_once CAUTHPATH."/config.php";
	require_once CAUTHPATH."/cAuthProviderModel.php";
	require_once CAUTHPATH."/cAuth.php";
	require_once CAUTHPATH."/cAuth_session.php";
?>