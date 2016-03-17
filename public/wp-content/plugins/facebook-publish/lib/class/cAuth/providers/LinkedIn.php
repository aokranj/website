<?php 


class LinkedInProvider extends cAuthProviderModel {

	public $provider_id 		= "LinkedIn";
	private $enabled			= ""; 
	private $id					= ""; 
	private $secret				= ""; 
	private $return_url			= ""; 
	private $api				= ""; 


	function __construct($provider_info , $return_url) {
				
		require_once CAUTH_THIRD_PARTY_AUTH."/LinkedIn/Lib/LinkedIn.php";
		$this->enabled		= $provider_info["enabled"];
		$this->id			= $provider_info["id"];
		$this->secret		= $provider_info["secret"];
		$this->return_url	= $return_url;

		$callback_url		= $this->return_url;
		if(isset($_GET["cauth_return"])) {
			$callback_url = cAuth_session::get_provider_val('start_url' ,   $this->provider_id );
		}

		$this->api = new LinkedIn(
						  array(
						    'api_key' 		=> $this->id, 
						    'api_secret' 	=> $this->secret, 
						    'callback_url' 	=> $callback_url,

						  )
						);


		$session_data = cAuth_session::get_session();

		if(isset($session_data[$this->provider_id]["logged_in"])) {

			$this->set_access_token(cAuth_session::get_provider_val('oauth_token' , $this->provider_id ));
		
		}



	}

	function login_begin($redirect_url="") {

		$auth_url = $this->get_login_url();

		header("location:$auth_url"); exit;
	}

	function login_end() {
			
		if(isset($_REQUEST['code'])) {
		    $token = $this->api->getAccessToken($_REQUEST['code']);
		    $token_expires = $this->api->getAccessTokenExpiration();
			cAuth_session::set_provider_val('oauth_token' ,   $this->provider_id , $token);
			cAuth_session::set_provider_val('token_expiration' ,   $this->provider_id , $token_expires);
			cAuth_session::set_provider_val( "logged_in" , $this->provider_id , true );
			return $this;
		}
		else {

			return false;
		
		}
	}

	function get_login_url($provider="", $scope="") {
		return $this->api->getLoginUrl(
		  array(
		    LinkedIn::SCOPE_BASIC_PROFILE, 
		    LinkedIn::SCOPE_EMAIL_ADDRESS, 
		    LinkedIn::SCOPE_NETWORK,
		    LinkedIn::SCOPE_READ_WRTIE_UPDATES,
		    LinkedIn::SCOPE_READ_WRITE_GROUPS,
		  )
		);
	}

	function post_status($status) {

		 $post = array(
            'comment' => 'Test social Share',
            'content' => array(
                            'title' => $status["message"],
                            'description' => $status["message"], //Maxlen(255)
                            'submitted_url' => $status["link"]
                            ),
            'visibility' => array(
                        'code' => 'anyone'
                        )

            );
    	$reply = $this->api->post('people/~/shares', $post);
		return $reply;
	}

	function api() {
		return $this->api;
	}

	function get_access_token() {

		return "";
	}

	function set_access_token($token) {

		$this->api->setAccessToken($token);
		
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
