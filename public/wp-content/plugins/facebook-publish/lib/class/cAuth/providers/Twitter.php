<?php 

//use Codebird;

class TwitterProvider extends cAuthProviderModel {

	public $provider_id 		= "Twitter";
	private $enabled			= ""; 
	private $id					= ""; 
	private $secret				= ""; 
	private $redirect_url		= ""; 
	private $api				= ""; 


	function __construct($provider_info , $redirect_url) {
				
		require_once CAUTH_THIRD_PARTY_AUTH."/Twitter/codebird/codebird.php";
		$this->enabled		= $provider_info["enabled"];
		$this->id			= $provider_info["id"];
		$this->secret		= $provider_info["secret"];
		$this->redirect_url	= $redirect_url;

		Codebird\Codebird::setConsumerKey($this->id, $this->secret); 

		$this->api = Codebird\Codebird::getInstance();
		$session_data = cAuth_session::get_session();
		if(isset($session_data[$this->provider_id]["logged_in"])) {

			$token = array(
							"oauth_token"			=> cAuth_session::get_provider_val('oauth_token' ,   $this->provider_id) , 
							"oauth_token_secret"	=> cAuth_session::get_provider_val('oauth_token_secret' ,   $this->provider_id) 
							);

			//pre($token);
			$this->set_access_token($token);
		
		}



	}

	function login_begin($redirect_url="") {

	    $reply = $this->api->oauth_requestToken(array(
	        'oauth_callback' => $redirect_url
	    ));

	    // store the token
	    $session_data = cAuth_session::get_session();
	    $this->api->setToken($reply->oauth_token, $reply->oauth_token_secret);
	    //$session_data[$this->provider_id]['oauth_token'] = $reply->oauth_token;
	    //$session_data[$this->provider_id]['oauth_token_secret'] = $reply->oauth_token_secret;
	    //$session_data[$this->provider_id]['oauth_verify'] = true;
	    cAuth_session::set_provider_val('oauth_token' ,   $this->provider_id , $reply->oauth_token);
	    cAuth_session::set_provider_val('oauth_token_secret' ,   $this->provider_id , $reply->oauth_token_secret);
	    cAuth_session::set_provider_val('oauth_verify' ,   $this->provider_id , true);

	    // redirect to auth website
	    $auth_url = $this->api->oauth_authorize();

		header("location:$auth_url"); exit;
	}

	function login_end() {
		
		$session_data = cAuth_session::get_session();
		$this->api->setToken($session_data[$this->provider_id]['oauth_token'], $session_data[$this->provider_id]['oauth_token_secret']);
    	unset($session_data[$this->provider_id]['oauth_verify']);
    	$reply = $this->api->oauth_accessToken(array(
        				'oauth_verifier' => $_GET['oauth_verifier']
    				));

    	cAuth_session::set_provider_val('oauth_token' ,   $this->provider_id , $reply->oauth_token);
   		cAuth_session::set_provider_val('oauth_token_secret' ,  $this->provider_id , $reply->oauth_token_secret);
		cAuth_session::set_provider_val( "logged_in" , $this->provider_id , true );
		//$this->api->setToken($_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);
		
		return $this;

	}

	function get_login_url($provider="", $scope="") {

	}

	function post_status($status) {

		$params = array(
						"status" => $status["message"],
					);

		if(isset($status["picture"])) {
			
			$reply = $this->api->media_upload(array(
									        'media' => $status["picture"]
									    ));
			

			pre($reply);						    
			$params["media_ids"] = $reply->media_id_string;
			
		}
		//$graphObject = $response->getGraphObject();
		
		$reply = $this->api->statuses_update($params);
		return $reply;
	}

	function api() {
		return $this->api;
	}

	function get_access_token() {

		return "";
	}

	function set_access_token($token) {

		$this->api->setToken($token["oauth_token"], $token["oauth_token_secret"]);
		
	}

	function get_name() {
		/*$request = new FacebookRequest(
								  $this->api,
								  'GET',
								  '/me'
								);
		$response = $request->execute();
		$graphObject = $response->getGraphObject();
		return $graphObject->getProperty('name');*/
	}

	function get_feeds() {

	}	

	function logout() {
		$session_data = cAuth_session::get_session();
		unset($_SESSION["cauth"][$this->provider_id]) ;	
	}

}
