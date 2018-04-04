<?php 

use Facebook\FacebookSession;
use Facebook\FacebookRequest;
use Facebook\GraphUser;
use Facebook\FacebookRequestException;
use Facebook\FacebookRedirectLoginHelper;

	
class FacebookProvider extends cAuthProviderModel {

	public $provider_id 		= "Facebook";
	private $enabled			= ""; 
	private $id					= ""; 
	private $secret				= ""; 
	private $redirect_url		= ""; 
	private $api				= ""; 


	function __construct($provider_info , $redirect_url) {
				
		require_once CAUTH_THIRD_PARTY_AUTH."/Facebook/facebook-php-sdk-v4-4.0-dev/autoload.php";
		$this->enabled		= $provider_info["enabled"];
		$this->id			= $provider_info["id"];
		$this->secret		= $provider_info["secret"];
		$this->redirect_url	= $redirect_url;

		FacebookSession::setDefaultApplication($this->id,$this->secret);

		$session_data = cAuth_session::get_session();
		if(isset($session_data[$this->provider_id]["logged_in"])) {

			$this->set_access_token($session_data[$this->provider_id]["token"]);
		}



	}

	function login_begin($redirect_url="") {

		$login_helper = new FacebookRedirectLoginHelper($redirect_url);
		//echo $redirect_url; exit;
		$params		  = array(
								"scope" => "public_profile,publish_actions,manage_pages,publish_pages"	
								);

		$fb_login_url  = $login_helper->getLoginUrl($params);

		header("location:$fb_login_url"); exit;
	}

	function login_end() {
				
		$start_url = cAuth_session::get_provider_val( "start_url" , $this->provider_id ); 

		$login_helper = new FacebookRedirectLoginHelper($start_url);
		
		try{
			$this->api = $login_helper->getSessionFromRedirect();	
			$token =  $this->api->getToken();	
			
			cAuth_session::set_provider_val( "logged_in" , $this->provider_id , true );
			cAuth_session::set_provider_val( "token" , $this->provider_id ,  $token);
			return $this;	
		}
		catch(Exception $ex) {
			die( $ex->getMessage() ); 
		}
	}

	function get_login_url($provider="", $scope="") {
	}

	function post_status($status) {

		$edge = "/me/feed";

		if(isset($status["picture"]) && isset($status["link"])) {
			if(!isset($status["message"])) {
				$status["message"] = $status["picture"];
			}
			else {
				$status["message"] .= " ".$status["link"];
			}
		}

		if(isset($status["picture"])) {
			$edge 			= "/me/photos";
			$status["url"]	= $status["picture"];
			unset($status["picture"]);
		}

		$request = new FacebookRequest(
								  $this->api,
								  'POST',
								  $edge,
								  $status
								);
	
		$response = $request->execute();
		return $response;
		//$graphObject = $response->getGraphObject();
		
	}

	function api() {
		return $this->api;
	}

	function get_access_token() {

		return $this->api()->getToken();
	}

	function set_access_token($session) {
		$session = new FacebookSession($session);
		$this->api = $session;
	}

	function get_name() {
		$request = new FacebookRequest(
								  $this->api,
								  'GET',
								  '/me'
								);
		$response = $request->execute();
		$graphObject = $response->getGraphObject();
		return $graphObject->getProperty('name');
	}

	function get_feeds() {

	}	

	function logout() {
		unset($_SESSION["cauth"][$this->provider_id]) ;	
	}

}

?>