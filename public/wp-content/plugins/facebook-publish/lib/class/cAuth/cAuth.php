<?php 

class cAuth {

	function __construct() {

		if(!isset($_SESSION)) {
			session_start();
		}

		$this->include_providers();
	}

	
	function include_providers() {


		foreach (glob(CAUTHPATH."/providers/*.php") as $filename)
		{
		    require_once($filename);
		}

	
	}	

	public function authenticate($provider) {

		global $cauth_config;

		$provider_class = $provider."Provider";
		if($this->provider_exist($provider)) {
			
			$adapter = new $provider_class( $cauth_config["providers"][$provider] , self::get_return_url($provider) );
			
			if(isset($_GET["cauth_return"])) {
				return $this->handle_cauth_return($adapter);
			}
			else {
				if(!$this->is_connected_with($provider)) {
					$this->prepare_authorization($adapter);
				}
				else {
					return $this->get_provider_adapter($provider);
				}
			}		

		}

	}	




	function prepare_authorization($adapter) {

		global $cauth_config; 

		$time = time();
		
		$cauth_session_id 	= $adapter->provider_id.":".$time;

		$return_url 		= self::get_return_url($adapter->provider_id);

		cAuth_session::set_provider_val( "id" , $adapter->provider_id , $cauth_session_id );  
		cAuth_session::set_provider_val( "start_url" , $adapter->provider_id , $return_url );  
		cAuth_session::set_provider_val( "config" , $adapter->provider_id , $cauth_config["providers"][$adapter->provider_id] );  

		$adapter->login_begin($return_url);

	}

	function handle_cauth_return($adapter) {
		
		return $adapter->login_end();

	}


	function provider_exist($provider) {
		
		if( !isset($cauth_config["providers"][$provider]) ) {
			$provider_class = $provider."Provider";

			if(class_exists($provider_class)) {
				return true;
			}
			else {
				die("$provider_class class not exists."); 
			}

		}
		else {
			die($provider." not defined in config array");
		}

		return false;
	
	}

	public function get_session_data() {
		return serialize(cAuth_session::get_session());
	}


	public function load_session_data($data) {

		$_SESSION["cauth"] = unserialize($data);
	
	}

	public function is_connected_with($provider_id) {

		$session_data = cAuth_session::get_session();

		return ( isset($session_data[$provider_id]["logged_in"]) &&  $session_data[$provider_id]["logged_in"] );


	}

	public function get_provider_adapter($provider_id) {

		global $cauth_config;

		$provider_class = $provider_id."Provider";
		if($this->provider_exist($provider_id) && $this->is_connected_with($provider_id)) {
			
			$adapter = new $provider_class( $cauth_config["providers"][$provider_id] , current_page_url() );
			
			return $adapter;

		}
	}

	public function logout_all_providers() {
		$_SESSION["cauth"] = "";
	}

	static function get_return_url($provider_id) {


		$current_page_url 	= current_page_url();
		$seperator 			= (parse_url($current_page_url, PHP_URL_QUERY) == NULL) ? '?' : '&';
		$return_url 		= $current_page_url.$seperator."cauth_return=".$provider_id;
		return $return_url;
	}

}