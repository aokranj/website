<?php
		
	global $fp_settings;
	global $fp_version;
	
	//delete options only after version 1.4
	delete_option("fp_settings");

	$fp_settings = get_option("fp_settings");
	
	

	$defaults = array(
						'version' 				=> $fp_version,
						'ptf_app_id' 			=> "",
						'ptf_api_key' 			=> "",
						'fp_post_types' 		=> array("post" , "page"),
						'fp_post_cat' 			=> array("Uncategorized"),
						'fp_all_posts' 			=> "0",
						'show_auth_btn' 		=> true,
						'msg_body'				=> "My new post on {SITE_NAME}", 
						'post_featured_image'	=> "featured", 
						'post_schedule' 		=> "no",
						'queue' 				=> array(),
						'session_data' 			=> "",
						'pages' 				=> array(), 
						'global_pages' 			=> array( "own"=> array("id" => "own" , "name" => "Own Timeline")  ), 
						'automatic_posts' 		=> "no",
						'post_count' 			=> 0,
					);

	//$fp_settings = wp_parse_args( $fp_settings, $defaults );

	//updating new values
	foreach($defaults as $key=>$default) {

		if(!isset($fp_settings[$key])) {
			$fp_settings[$key] = $default;
		
		}

	}

	$fp_settings["version"] = $fp_version;

	update_option( "fp_settings", $fp_settings );
