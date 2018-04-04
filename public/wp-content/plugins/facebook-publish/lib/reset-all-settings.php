<?php

add_action("init" , "fp_reset_plugin_data");

function fp_reset_plugin_data() {
	
	if(isset($_GET["fp_reset_plugin_data"])) {
		
		delete_option("fp_settings");
		
		//set the default value
		require ABSPATH."/wp-content/plugins/facebook-publish/fp_activate_plugin.php";
		
		//showing message
		cdlc_show_notification("Plugin data reset.");

		//redirect to general settings page
		wp_redirect(site_url("/wp-admin/admin.php?page=facebook-publish&tab=general"));
	}

}

