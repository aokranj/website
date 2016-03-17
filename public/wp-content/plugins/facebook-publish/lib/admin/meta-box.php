<?php

function fp_add_meta_box() {
	global $fp_settings;
	
	global $post;
    if(!isset($post))
        return;
    
	$screens = array();
	
	$post_types = get_post_types( '', 'names' );
	foreach ( $post_types as $post_type ) { 
		$screens[] = $post_type;
				
	}
	
	foreach ( $screens as $screen ) {
		add_meta_box(
			'fp_meta',
			'Facebook Publish Settings',
			'fp_meta_box_callback',
			$screen,
			'side',
			'high'
		);
	}
//fp_add_meta_box()	
}
add_action( 'add_meta_boxes', 'fp_add_meta_box' );

function fp_meta_box_callback() {
	global $fp_settings;

	$publish_this 	= "no";
	$post_type 		= "";	
	if($fp_settings['fp_all_posts'] == 1) {
		
		$publish_this = "yes";
	
	}
	else {
		//getting current post type
		if(isset($_GET['post_type'])) {
			$post_type = $_GET['post_type'];
		
		}
		else {
			$post_type = "post";
		}
		//checking if current post type matches with the allowed post types 
		if(in_array($post_type, $fp_settings['fp_post_types'])) {
			
			$publish_this = "yes";
			 
		}
	}
	if(isset($_GET['post'])) {

		$publish_this = "no";
	}
	?>
	
		<input <?php  checked($publish_this , "yes"); ?> type="checkbox" id="fp_publish_this" name="fp_publish_this" ><label for="fp_publish_this">Publish this on Facebook?</label>
		
		<div class="fp_child fp_show_on_publish">
		
			<input <?php  checked($fp_settings['post_schedule'] , "no"); ?> type="radio" id="fp_schedule_this_no"  value="no"   name="fp_schedule_this"><label for="fp_schedule_this_no">Post immediately on Facebook	</label><br>
		
			<input <?php  checked($fp_settings['post_schedule'] , "yes"); ?> type="radio" id="fp_schedule_this_yes" value="yes"  name="fp_schedule_this"><label for="fp_schedule_this_yes">Let me schedule.</label>
		
			<div class="fp_child fp_show_on_schedule fp_hidden">
				<input type="text" name="fp_datetime" class="fp_datetime"  data-field="datetime" readonly>
			
			</div>
			<br>
			<!-- More settings-->
	
			<small id="override_global"><span id="more_symbol">[+]</span> Override Global settings</small>
			
			<div class="fp_child fp_hidden" id="global_options_body">
				
				<div class="fp_msg_body_contaniner">
					<label for="fp_msg_body"  class="fp_label">Message Body:</label><br>
					<textarea id="fp_msg_body" name="msg_body" ><?php echo $fp_settings["msg_body"]; ?></textarea>
				</div>
				
				<div class="fp_feature_img_container">
					<span  class="fp_label">Publish Image:</span><br>
					<input checked="checked" type="radio" id="fp_featured_img_featured" value="featured" name="fp_featured_img" /> <label for="fp_featured_img_featured">Use Featured Image</label><br>
					<input type="radio" id="fp_featured_img_none"  value="none"  name="fp_featured_img" /> <label for="fp_featured_img_none">None</label><br>
					<input type="radio" id="fp_featured_img_choose"  value="choose"  name="fp_featured_img" /> <label for="fp_featured_img_choose">Choose Image</label>
					<div class="fp_custom_img hidden">
						<div class="uploader">
							<input id="fp_post_img_text" name="fp_post_img_text" type="text" />
							<input id="fp_post_img_btn" class="fp_post_img_btn button" name="fp_post_img_btn" type="button" value="Upload" />
						</div>
					</div>
				</div>				
				

				<div class="fp_fb_pages">
					<span class="fp_label">Facebook Pages:</span><br>
					<?php fp_print_fb_pages(); ?>
				</div>
				 <div id="dtBox"></div>
				<input type="hidden" value="" name="fp_timezone_offset" id="fp_timezone_offset"/>
			</div>			
		</div>
		<?php 

		if(isset($_GET['post'])) {
			$post_id = $_GET['post'];
			if(fp_post_is_scheduled($post_id)) {

				$post_settings 	= get_post_meta($post_id , "post_settings" , true);
				$fp_datetime 	= $post_settings["fp_datetime"];
				echo "<div class='fp_scheduled_post'>Scheduled: $fp_datetime</div>";
			
			}

		}

		?>		
		<style type="text/css">
			.fp_child {
				width: 100%;
				padding-left:20px;
				box-sizing: border-box;
				padding-top:10px;
			}
			#fp_meta {
			  background: none repeat scroll 0 0 #81b5fc;
			}

			.fp_hidden{
				display: none;
			}
			#override_global {
			  color: #565656;
			  cursor: pointer;
			}
			.fp_label {
				font-weight: bold;
			}
			#fp_post_img_text {
			  width: 124px;
			}
			/* #fp_post_img_btn {
			  width: 59px;
			} */
			.fp_scheduled_post {
			  background: none repeat scroll 0 0 black;
			  color: #81b5fc;
			  font-size: 12px;
			  padding: 2px 5px;
			  margin-top: 10px;

			}
		</style>
		<script type="text/javascript">
			function fg_toggle_publish() {
				if(jQuery("#fp_publish_this").is(":checked")) {
	  
				  jQuery(".fp_show_on_publish").fadeIn();
				  
				}
				else {
				  
				    jQuery(".fp_show_on_publish").fadeOut();

				}
			}
			function fp_toggle_schedule() {
				if(jQuery("#fp_schedule_this_yes").is(":checked")) {
  
				  jQuery(".fp_show_on_schedule").fadeIn();
				  
				}
				else {
				  
				    jQuery(".fp_show_on_schedule").fadeOut();
				}
			}
			function fp_override_global_option_effects() {
				jQuery("#override_global").click(
				  function() {
				    
				    jQuery("#global_options_body").fadeToggle();
				    
				    if(jQuery("#more_symbol").html() == "[+]")
				    {
				      jQuery("#more_symbol").html("[-]");
				    }
				    else {
				      jQuery("#more_symbol").html("[+]");
				    }
				    
				});
			}
			function fp_add_timzone_value() {
				var offset = new Date().getTimezoneOffset();
				jQuery("#fp_timezone_offset").val(offset);
			}
			function fp_scheduling_validation() {
				jQuery("form#post").submit( function() {
  
				  if(jQuery("#fp_schedule_this_yes").is(":checked") && jQuery("#fp_publish_this").is(":checked")) {
				    
				    if(jQuery(".fp_datetime").val() == "") {
				        
				      alert("Please enter date and time for post scheduling. ");
				      jQuery(".fp_datetime").focus();
				      return false;
				      
				    }
				    
				  }
				  
				});
			}

			

			function handle_featured_img() {

				if( jQuery("#fp_featured_img_choose").is(":checked") ) {
			      jQuery(".fp_custom_img").fadeIn();
			    }
			    else {
			      jQuery(".fp_custom_img").fadeOut();    
			    }
			    
			} 
			jQuery(document).ready(
			function(){
				fp_toggle_schedule();
				fg_toggle_publish();	
				jQuery("#fp_publish_this").change(
				function() {
					fg_toggle_publish();	
				});

				handle_featured_img();
				jQuery("input[name='fp_featured_img']").change(handle_featured_img);

				jQuery("input[name=fp_schedule_this]").change(
				function() {
					fp_toggle_schedule();	
				});
				fp_override_global_option_effects();
				jQuery(".fp_datetime").datetimepicker();
				
				fp_add_timzone_value();
				fp_scheduling_validation();
			});

			jQuery(document).ready(function($){
				var _custom_media = true,
				_orig_send_attachment = wp.media.editor.send.attachment;
				 
				$('.fp_post_img_btn').click(function(e) {
				var send_attachment_bkp = wp.media.editor.send.attachment;
				var button = $(this);
				var id = button.attr('id').replace('_button', '');
				_custom_media = true;
				wp.media.editor.send.attachment = function(props, attachment){
				if ( _custom_media ) {
				$("#fp_post_img_text").val(attachment.url);
				} else {
				return _orig_send_attachment.apply( this, [props, attachment] );
				};
				}
				 
				wp.media.editor.open(button);
				return false;
				});
				 
				$('.add_media').on('click', function(){
				_custom_media = false;
				});
			});
		</script>
	<?php
//fp_meta_box_callback
}
