<?php 

abstract class cAuthProviderModel {

	/*abstract function initialize($provider_info);*/
	abstract function login_begin($redirect_url="");
	abstract function login_end();
	abstract function get_login_url($provider="", $scope="" );
	abstract function post_status($status);
	abstract function api();
	abstract function get_access_token();
	abstract function set_access_token($session);
	abstract function get_name();
	abstract function get_feeds();
	abstract function logout();

}