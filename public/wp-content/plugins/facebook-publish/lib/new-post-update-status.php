<?php
use Facebook\FacebookRequest;

add_action( 'save_post', 'fp_publish_post' );

function fp_publish_post($post_id) {

	global $fp_cauth;
	global $fp_settings;
	
	if ( wp_is_post_revision( $post_id )   ) {
		return;
	}

	//if post is being saved as a draft, dont proceed 
	if(isset($_POST["save"]) && $_POST["save"] == "Save Draft") {
		return;
	}
	
	//if new post then dont proceed
	if(get_post_status( $post_id ) == "auto-draft") {
		return;
	}

	if($fp_cauth->is_connected_with("Facebook")) {

		if(isset($_POST['fp_publish_this'])) {
			fp_post_handler($post_id);
		}
		else if($fp_settings["automatic_posts"]=="yes" && ( in_array( get_post_type( $post_id ) , $fp_settings["fp_post_types"] ) || $fp_settings["fp_all_posts"] ) ) {
			
			fp_post_handler($post_id);

		}
		
	
	}
	else {
		cdlc_show_notification("Post couldn't be published on Facebook. " , "error"); 
	}
}

function fp_post_handler($post_id) {
	
	global $fp_settings;
	
	$config = array();
	
	$config['post_id'] 				= $post_id;
	
	$config['fp_publish_this'] 		= isset($_POST['fp_publish_this']) ? $_POST['fp_publish_this'] : "on";

	$config['fp_schedule_this'] 	= isset($_POST['fp_schedule_this']) ? $_POST['fp_schedule_this'] : "no";
	
	$config['fp_datetime'] 			= isset($_POST['fp_datetime']) ? $_POST['fp_datetime'] : "";
	
	$config['fp_timezone_offset'] 	= isset($_POST['fp_timezone_offset'])? $_POST['fp_timezone_offset'] : "";
	
	$config['msg_body'] 			= isset($_POST['msg_body'])? $_POST['msg_body'] : $fp_settings['msg_body'] ;
	$config['msg_body'] 			= stripslashes($config['msg_body']); 
	
	$config['fp_featured_img'] 		= isset($_POST['fp_featured_img']) ? $_POST['fp_featured_img'] : $fp_settings["post_featured_image"];
	
	$config['fp_post_img_text'] 	= isset($_POST['fp_post_img_text']) ? $_POST['fp_post_img_text'] : "";
	
	if(isset($_POST['global_pages'])) {
		$config['pages'] 				= $_POST['global_pages'];
	}
	else {
		
		$config['pages'] 				= $fp_settings["global_pages"];
	}
	if($config['fp_publish_this'] == "on" )
	{	
		if($config['fp_schedule_this'] == "yes") {
				
			fp_schedule_this_post($config);
		}
		else {
			fp_post_to_fb($config);
		}
	}

}

function fp_post_to_fb(  $post_settings ) {
	global $fp_cauth;
	
	$fp_settings = get_option("fp_settings");

	if( $fp_session_data = $fp_settings['session_data'] ) {
		$fp_cauth->load_session_data( $fp_session_data );
	}

	$facebook_adapter 	= $fp_cauth->get_provider_adapter("Facebook");
	$msg_body 			= $post_settings['msg_body'];
	$post_id 			= $post_settings["post_id"];
	$message 			= render_magic_quote($post_id , $msg_body);
	$url 				= get_permalink( $post_settings["post_id"] );
	$picture 			= array();
	$config			 	=  array(
								"message" => $message, 
								"link" => $url,  
								); 

	if($post_settings['fp_featured_img']=="featured") {
		if(has_post_thumbnail( $post_id )) {
			
			$img = wp_get_attachment_image_src( get_post_thumbnail_id($post_id), 'large');
			$img = $img[0];
			$picture = array("picture" =>   $img);
			$config  = array_merge($config , $picture);
			
		}

	}
	else if($post_settings['fp_featured_img']== "choose") {
		
		if($post_settings['fp_post_img_text']) {

			$picture = array("picture" =>   $post_settings['fp_post_img_text']);
			
			$config  = array_merge($config , $picture);
		}
	}

	if(in_array(  "own" , $post_settings['pages'])){
		try{
			$facebook_adapter->post_status( $config );
		}
		catch(Exception $e) {
			cdlc_show_notification("Post couldn't be published on Facebook. ".$e->getMessage() , "error"); 
			return;
		}
	}

	foreach($post_settings['pages'] as $page_id ) {
		if($page_id != "own") {
			
			fp_post_to_fb_page($page_id , $post_settings );			
		}
		
	}
	cdlc_show_notification("Post published on Facebook");
}

function fp_schedule_this_post($post_settings) {

	global $fp_settings;
	$post_id 			= $post_settings['post_id'];
	$fp_datetime 		= $post_settings['fp_datetime'];
	$fp_timezone_offset = $post_settings['fp_timezone_offset'];
	$date 				= new DateTime($fp_datetime);
	$timestamp 			= $date->getTimestamp();	
	$timestamp 			= $timestamp + ($fp_timezone_offset*60);
	$queue				= $fp_settings["queue"];	
	$queue[]			= $post_id;

	update_post_meta($post_id , "fp_timestamp" , $timestamp  );	
	update_post_meta($post_id , "post_settings" , $post_settings  );			
	
	$fp_settings["queue"] =   array_unique($queue);
	
	update_option( "fp_settings" , $fp_settings);

	cdlc_show_notification("Post scheduled for date: ".$fp_datetime );

}

if ( ! wp_next_scheduled( 'fp_cron_hook' ) ) {
  wp_schedule_event( time(), 'hourly', 'fp_cron_hook' );
}

add_action( 'fp_cron_hook', 'fp_cron_function' );

function fp_cron_function() {
	$fp_settings = get_option("fp_settings");
	//pre($fp_settings["queue"]);
	if(count($fp_settings["queue"]) > 0) {
		
		$current_timestamp = time();
		//getting posts in the queue and have their publish timestamp more then current timestamp
		$args = array(
					'post_type'	 => $fp_settings["fp_post_types"],
					'post__in' 	  => $fp_settings["queue"],
					'meta_query'  => array(
										array(
											'key'       => 'fp_timestamp',
											'value'     => $current_timestamp,
											'compare'   => '<',
											'type'      => 'NUMERIC',
										),
									),
				);
		// The Query
		$query = new WP_Query( $args );	
		if($query->have_posts()) {
		
			while($query->have_posts()) {
		
				$query->the_post();
				$post_id = get_the_ID();
				$post_settings = get_post_meta( $post_id , "post_settings" , true );
				fp_post_to_fb( $post_settings );	 		
				//remove current post id from queue 
				foreach($fp_settings["queue"] as $key => $id) {
					
					if($post_id == $id) { 	unset($fp_settings["queue"][$key]);		}
					
				}
			}
		}
		else {
			echo "no post";
		}
		update_option("fp_settings" , $fp_settings);
	}
}

function fp_post_to_fb_page($page_id, $post_settings ) {

	global $fp_settings, $fp_cauth;

	$facebook_adapter 	= $fp_cauth->get_provider_adapter("Facebook");
	$message 			= $post_settings['msg_body'];
	$post_id 			= $post_settings["post_id"];
	$message 			= render_magic_quote($post_id , $message);
	
	$picture 			= array();
	
	$url 				= get_permalink( $post_settings["post_id"] );
	$post_id 			= $post_settings['post_id'];
	$params 			= array(
								'access_token' => $fp_settings['pages'][$page_id]['access_token'],
								'message' => $message,
								"link" => $url, 
								);

	if($post_settings['fp_featured_img']=="featured") {
		
		if(has_post_thumbnail( $post_id )) {
			
			$img = wp_get_attachment_image_src( get_post_thumbnail_id($post_id), 'large');
			$img = $img[0];
			$picture = array("picture" =>   $img);
			
			$params  = array_merge($params , $picture);
			
		}
	}
	else if($post_settings['fp_featured_img']== "choose") {
		
		if($post_settings['fp_post_img_text']) {

			$picture = array("picture" =>   $post_settings['fp_post_img_text']);
			
			$params  = array_merge($params , $picture);
		}
	}
	

	try{
/*
		$request = new FacebookRequest($facebook_adapter->api(), 'GET', '/me/accounts?fields=name,access_token,perms');
		$pageList = $request->execute()
		  ->getGraphObject()
		  ->asArray();
*/

		//pre($pageList); exit;
		/*foreach($pageList["data"] as $page) {
			if($page->id == $page_id) {
				$params["access_token"] = $page->access_token;	
				pre($page);
			}
		}*/
		//$facebook_adapter->api()->api( "/" . $page_id . "/feed", 'POST', $params );
		//unset($params["access_token"]);
		//$request = new FacebookRequest( $facebook_adapter->api(), 'POST', "/$page_id/feed", $params );
		$request = new FacebookRequest( $facebook_adapter->api(), 'POST', "/$page_id/feed", $params );
		
		$response = $request->execute();
		$graphObject = $response->getGraphObject();
		fp_increment_post_count();
	}
	catch(Exception $e) {

		cdlc_show_notification("Post couldn't be published on Facebook. ".$e->getMessage() , "error"); 
		return;

	}
}

function render_magic_quote($post_id , $msg) {
	$post = get_post($post_id); 
	
	$POST_TITLE = '{POST_TITLE}';
	if(strpos($msg, $POST_TITLE) !== false) {
		$title = $post->post_title;
		$msg = str_replace($POST_TITLE , $title, $msg);
	}
	$POST_URL = '{POST_URL}';
	if(strpos($msg, $POST_URL) !== false) {
		$url =  get_permalink( $post_id );
		$msg = str_replace($POST_URL , $url, $msg);
	}
	$SITE_URL = '{SITE_URL}';
	if(strpos($msg, $SITE_URL) !== false) {
		$url = get_site_url();
		$msg = str_replace($SITE_URL , $url, $msg);
	}
	
	$POST_ID = '{POST_ID}';
	if(strpos($msg, $POST_ID) !== false) {
		$msg = str_replace($POST_ID , $post_id, $msg);
	}
	
	$POST_EXCERPT = '{POST_EXCERPT}';
	if(strpos($msg, $POST_EXCERPT) !== false) {
		$excerpt = $post->post_excerpt;
		$excerpt = strip_tags($excerpt);
		$msg 	 = str_replace($POST_EXCERPT , $excerpt , $msg);

	}		

	$SITE_NAME = '{SITE_NAME}';
	if(strpos($msg, $SITE_NAME) !== false) {
		$sitename =  get_bloginfo( "name" );
		$msg = str_replace($SITE_NAME , $sitename , $msg);
	}	
	
	//TODO author is incomplete 
	$POST_AUTHOR = '{POST_AUTHOR}';
	if(strpos($msg, $POST_AUTHOR) !== false) {
		$author_id 	= $post->post_author;
		$author 	= get_userdata($author_id);
		$author 	= $author->user_nicename;
		$author 	= ucwords($author);	
		$msg 		= str_replace($POST_AUTHOR , $author , $msg);
	}
	return $msg;
}
?>