<?php 

class cAuth_session {

	static function get_provider_val($key , $provider) {
		$session = cAuth_session::get_session();
		if(isset($session[$provider][$key])) {
			return $session[$provider][$key]; 			
		} 
		else {
			return "";
		}
	}
	
	static function set_provider_val( $key , $provider , $val ) {
		$session = cAuth_session::get_session();
		$session[$provider][$key] = $val;
		cAuth_session::set_session($session);
	}

	static function get_session() {
		if(isset($_SESSION["cauth"]) ) {
			return $_SESSION["cauth"];
		}
		else {
			return;
		}
	}

	static function set_session($data) {
		//$data = serialize( $data );	
		$_SESSION["cauth"] = $data;
		
	}

	static function reset_session() {
		$_SESSION["cauth"] = "";
	}



}

