<?php 

$cauth_config = array(

				"base_url" 	=> site_url( "/wp-content/plugins/facebook-publish/lib/class/cAuth/" ),
				"providers" => array(
									"Facebook" => array(
														"enabled" 	=> "true",
														"id"		=> isset($fp_settings["ptf_app_id"])?$fp_settings["ptf_app_id"]:"",
														"secret"	=> isset($fp_settings["ptf_api_key"])?$fp_settings["ptf_api_key"]:"" ),			
													
									),

			); 

