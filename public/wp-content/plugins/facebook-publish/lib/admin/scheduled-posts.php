<?php

add_action("admin_menu" , "fp_settings_scheduledposts");

function fp_settings_scheduledposts() {

	add_submenu_page(FP_SETTINGS_SLUG, "Scheduled Posts" , "Scheduled Posts", "manage_options", FP_SETTINGS_SLUG."-scheduled-post" , "fp_settings_scheduledposts_page" ); 

}

function fp_settings_scheduledposts_page() {
	
	global $fp_settings;
	$post_status = 0;
	$icon_url =  plugins_url( '/assets/fp_logo.png' , __FILE__ );

	?>		
	<div class="wrap">		
			

		<h2><img src="<?php echo $icon_url ?>" alt="" class='fp_logo'>Scheduled Posts</h2>

		<hr>

		<table class="fp_general_settings wp-list-table widefat fixed posts"> 
			<thead>
			<tr>
				<th class="manage-column column-title">S.No</th>
				<th class="manage-column column-title">Post Title</th>
				<th class="manage-column column-title">Post Type</th>
				<th class="manage-column column-title">Dated</th>
				<th class="manage-column column-title"></th>
			</tr>
			</thead>
			<tbody>
				<?php

				$fp_settings = get_option("fp_settings");

				if(count($fp_settings["queue"]) > 0) {
					
					$current_timestamp = time();
					//getting posts in the queue and have their publish timestamp more then current timestamp
					$args = array(
								'post_type'	 => $fp_settings["fp_post_types"],
								'post__in' 	  => $fp_settings["queue"],
								'posts_per_page' => -1,
								'meta_query'  => array(
													array(
														'key'       => 'fp_timestamp',
														'value'     => $current_timestamp,
														'compare'   => '>',
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
							

							foreach($fp_settings["queue"] as $key => $id) {
								
								if($post_id == $id) { 	
									echo 	"<tr class='format-standard alternate'> 
												<td>".++$post_status."</td>	
												<td>".get_the_title()." </td>	
												<td>".get_post_type($post_id)."</td>	
												<td>".$post_settings["fp_datetime"]."</td>	
												<td><a href='".get_edit_post_link( $post_id ) ."' >Edit</a></td>	
											</tr>";

								}//if 
								
							} //foreach
						} //while
					} // if have_posts
				} //if count


				?>
			</tbody>
		</table>
		
	<?php	
	if($post_status == 0) {
		echo "<div class='fp_post_schedule' >No posts scheduled!</div>";
	}
	?>
	<style type="text/css">
	.fp_post_schedule {
	  border: 2px solid red;
	  padding: 5px;
	  text-align: center;
	}

	</style>
	<?php

}

