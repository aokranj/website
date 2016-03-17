<?php

add_action("admin_menu" , "fp_debug_info");

function fp_debug_info() {

	add_submenu_page(FP_SETTINGS_SLUG, "Debug Info" , "Debug Info", "manage_options", FP_SETTINGS_SLUG."-debug-info" , "fp_debug_info_page" ); 

}

function fp_debug_info_page() {
	
	global $fp_settings;
	$post_status = 0;
	$icon_url =  plugins_url( '/assets/fp_logo.png' , __FILE__ );

	?>		
	<div class="wrap">		
			

		<h2><img src="<?php echo $icon_url ?>" alt="" class='fp_logo'>Debug Info</h2>

		<hr>
		
		<code class="fpcode">
			
			<?php 
				
				echo fp_debuginfo(); 

			?>
			


		</code>

	<?php

}

