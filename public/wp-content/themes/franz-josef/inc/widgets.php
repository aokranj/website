<?php
/**
 * Top Bar: Menu widget
 */
class Franz_Top_Bar_Menu extends WP_Widget {
 
    public function __construct() {
        parent::__construct(
            'franz_top_bar_menu',
            __( 'Franz Josef Top Bar: Top Menu', 'franz-josef' ),
            array( 'description' => __( 'Display custom menu assigned to the Top Menu location.', 'franz-josef' ), )
        );
    }
 
    public function widget( $args, $instance ) {
        extract( $args );
		
        echo $before_widget;
		
		$class = 'top-menu';
		$class .= ' col-xs-12';
		if ( $instance['width'] == 9 ) $class .= ' col-sm-9';
		if ( $instance['width'] == 8 ) $class .= ' col-sm-8';
		if ( $instance['width'] == 6 ) $class .= ' col-sm-6';
		if ( $instance['width'] == 4 ) $class .= ' col-sm-4';
		if ( $instance['width'] == 3 ) $class .= ' col-sm-3';
		
		$text_align = ( isset( $instance['text_align'] ) ) ? $instance['text_align'] : 'left';
		$class .= ' text-align' . $text_align;
        ?>
        <div class="<?php echo $class; ?>">
			<?php 
				/* Header menu */
				$args = array(
					'theme_location'=> 'top-menu',
					'container'     => false,
					'echo'          => true,
					'fallback_cb'   => null,
					'items_wrap'    => '<ul id="%1$s" class="%2$s">%3$s</ul>',
					'depth'         => 1,
					'walker'		=> new FJ_Walker_Nav_Menu(),
				);
				
				wp_nav_menu( $args );
				
				do_action( 'franz_top_menu' );
			?>
		</div>
        <?php
		echo $after_widget;
    }
 
    public function form( $instance ) {
		$width = ( isset( $instance['width'] ) ) ? $instance['width'] : 6;
		$text_align = ( isset( $instance['text_align'] ) ) ? $instance['text_align'] : 'left';
        ?>
        <p>
            <label for="<?php echo $this->get_field_id( 'width' ); ?>"><?php _e( 'Width:', 'franz-josef' ); ?></label>
            <select id="<?php echo $this->get_field_id( 'width' ); ?>" name="<?php echo $this->get_field_name( 'width' ); ?>">
            	<option value="12" <?php selected( 12, $width ); ?>><?php _e( 'Full width', 'franz-josef' ); ?></option>
                <option value="9" <?php selected( 9, $width ); ?>><?php _e( '3/4 width', 'franz-josef' ); ?></option>
                <option value="8" <?php selected( 8, $width ); ?>><?php _e( '2/3 width', 'franz-josef' ); ?></option>
                <option value="6" <?php selected( 6, $width ); ?>><?php _e( 'Half width', 'franz-josef' ); ?></option>
                <option value="4" <?php selected( 4, $width ); ?>><?php _e( '1/3 width', 'franz-josef' ); ?></option>
                <option value="3" <?php selected( 3, $width ); ?>><?php _e( '1/4 width', 'franz-josef' ); ?></option>
            </select>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'text_align' ); ?>"><?php _e( 'Text alignment:', 'franz-josef' ); ?></label>
            <select id="<?php echo $this->get_field_id( 'text_align' ); ?>" name="<?php echo $this->get_field_name( 'text_align' ); ?>">
            	<option value="left" <?php selected( 'left', $text_align ); ?>><?php _e( 'Left', 'franz-josef' ); ?></option>
                <option value="center" <?php selected( 'center', $text_align ); ?>><?php _e( 'Center', 'franz-josef' ); ?></option>
                <option value="right" <?php selected( 'right', $text_align ); ?>><?php _e( 'Right', 'franz-josef' ); ?></option>
            </select>
        </p>
        <?php
	}
	
    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['width'] = ( ! empty( $new_instance['width'] ) ) ? strip_tags( $new_instance['width'] ) : '';
		$instance['text_align'] = ( ! empty( $new_instance['text_align'] ) ) ? strip_tags( $new_instance['text_align'] ) : '';
 
        return $instance;
    }
 
}


/**
 * Top Bar: Social Links
 */
class Franz_Top_Bar_Social extends WP_Widget {
 
    public function __construct() {
        parent::__construct(
            'franz_top_bar_social',
            __( 'Franz Josef Top Bar: Social Links', 'franz-josef' ),
            array( 'description' => __( 'Display social profile links.', 'franz-josef' ), 'panels_groups' => array( 'fj-stacks' ) )
        );
    }
 
    public function widget( $args, $instance ) {
        extract( $args );
		
        echo $before_widget;
		
		$class = '';
		$class .= ' col-xs-12';
		if ( $instance['width'] == 9 ) $class .= ' col-sm-9';
		if ( $instance['width'] == 8 ) $class .= ' col-sm-8';
		if ( $instance['width'] == 6 ) $class .= ' col-sm-6';
		if ( $instance['width'] == 4 ) $class .= ' col-sm-4';
		if ( $instance['width'] == 3 ) $class .= ' col-sm-3';
		
		$text_align = ( isset( $instance['text_align'] ) ) ? $instance['text_align'] : 'left';
		
		franz_social_links( array( 'classes' => array( $class ), 'text_align' => $text_align ) );
        
		echo $after_widget;
    }
 
    public function form( $instance ) {
		$width = ( isset( $instance['width'] ) ) ? $instance['width'] : 6;
		$text_align = ( isset( $instance['text_align'] ) ) ? $instance['text_align'] : 'left';
        ?>
        <p>
            <label for="<?php echo $this->get_field_id( 'width' ); ?>"><?php _e( 'Width:', 'franz-josef' ); ?></label>
            <select id="<?php echo $this->get_field_id( 'width' ); ?>" name="<?php echo $this->get_field_name( 'width' ); ?>">
            	<option value="12" <?php selected( 12, $width ); ?>><?php _e( 'Full width', 'franz-josef' ); ?></option>
                <option value="9" <?php selected( 9, $width ); ?>><?php _e( '3/4 width', 'franz-josef' ); ?></option>
                <option value="8" <?php selected( 8, $width ); ?>><?php _e( '2/3 width', 'franz-josef' ); ?></option>
                <option value="6" <?php selected( 6, $width ); ?>><?php _e( 'Half width', 'franz-josef' ); ?></option>
                <option value="4" <?php selected( 4, $width ); ?>><?php _e( '1/3 width', 'franz-josef' ); ?></option>
                <option value="3" <?php selected( 3, $width ); ?>><?php _e( '1/4 width', 'franz-josef' ); ?></option>
            </select>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'text_align' ); ?>"><?php _e( 'Text alignment:', 'franz-josef' ); ?></label>
            <select id="<?php echo $this->get_field_id( 'text_align' ); ?>" name="<?php echo $this->get_field_name( 'text_align' ); ?>">
            	<option value="left" <?php selected( 'left', $text_align ); ?>><?php _e( 'Left', 'franz-josef' ); ?></option>
                <option value="center" <?php selected( 'center', $text_align ); ?>><?php _e( 'Center', 'franz-josef' ); ?></option>
                <option value="right" <?php selected( 'right', $text_align ); ?>><?php _e( 'Right', 'franz-josef' ); ?></option>
            </select>
        </p>
        <?php
	}
	
    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['width'] = ( ! empty( $new_instance['width'] ) ) ? strip_tags( $new_instance['width'] ) : '';
		$instance['text_align'] = ( ! empty( $new_instance['text_align'] ) ) ? strip_tags( $new_instance['text_align'] ) : '';
 
        return $instance;
    }
 
}


/**
 * Top Bar: HTML widget
 */
class Franz_Top_Bar_Text extends WP_Widget {

	public function __construct() {
		$widget_ops = array( 'classname' => 'franz_top_bar_text', 'description' => __( 'Insert custom text or HTML.', 'franz-josef' ) );
		$control_ops = array('width' => 400, 'height' => 350);
		parent::__construct( 'franz_top_bar_text', __( 'Franz Josef Top Bar: Text', 'franz-josef' ), $widget_ops, $control_ops);
	}

	public function widget( $args, $instance ) {
		
		$class = 'text';
		$class .= ' col-xs-12';
		if ( $instance['width'] == 9 ) $class .= ' col-sm-9';
		if ( $instance['width'] == 8 ) $class .= ' col-sm-8';
		if ( $instance['width'] == 6 ) $class .= ' col-sm-6';
		if ( $instance['width'] == 4 ) $class .= ' col-sm-4';
		if ( $instance['width'] == 3 ) $class .= ' col-sm-3';
		
		$text_align = ( isset( $instance['text_align'] ) ) ? $instance['text_align'] : 'left';
		$class .= ' text-align' . $text_align;
		
		echo $args['before_widget'];
		?>
			<div class="<?php echo $class; ?>">
				<?php echo ! empty( $instance['filter'] ) ? wpautop( do_shortcode( $instance['text'] ) ) : do_shortcode( $instance['text'] ); ?>
            </div>
		<?php
		echo $args['after_widget'];
	}

	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		if ( current_user_can('unfiltered_html') )
			$instance['text'] =  $new_instance['text'];
		else
			$instance['text'] = stripslashes( wp_filter_post_kses( addslashes($new_instance['text']) ) ); // wp_filter_post_kses() expects slashed
		$instance['filter'] = ! empty( $new_instance['filter'] );
		
		$instance['width'] = ( ! empty( $new_instance['width'] ) ) ? strip_tags( $new_instance['width'] ) : '';
		$instance['text_align'] = ( ! empty( $new_instance['text_align'] ) ) ? strip_tags( $new_instance['text_align'] ) : '';
		
		return $instance;
	}

	public function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'text' => '' ) );
		$text = esc_textarea($instance['text']);
		$width = ( isset( $instance['width'] ) ) ? $instance['width'] : 6;
		$text_align = ( isset( $instance['text_align'] ) ) ? $instance['text_align'] : 'left';
?>
		<p><textarea class="widefat" rows="16" cols="20" id="<?php echo $this->get_field_id('text'); ?>" name="<?php echo $this->get_field_name('text'); ?>"><?php echo $text; ?></textarea></p>

		<p><input id="<?php echo $this->get_field_id('filter'); ?>" name="<?php echo $this->get_field_name('filter'); ?>" type="checkbox" <?php checked(isset($instance['filter']) ? $instance['filter'] : 0); ?> />&nbsp;<label for="<?php echo $this->get_field_id('filter'); ?>"><?php _e( 'Automatically add paragraphs', 'franz-josef' ); ?></label></p>
        
        <p>
            <label for="<?php echo $this->get_field_id( 'width' ); ?>"><?php _e( 'Width:', 'franz-josef' ); ?></label>
            <select id="<?php echo $this->get_field_id( 'width' ); ?>" name="<?php echo $this->get_field_name( 'width' ); ?>">
            	<option value="12" <?php selected( 12, $width ); ?>><?php _e( 'Full width', 'franz-josef' ); ?></option>
                <option value="9" <?php selected( 9, $width ); ?>><?php _e( '3/4 width', 'franz-josef' ); ?></option>
                <option value="8" <?php selected( 8, $width ); ?>><?php _e( '2/3 width', 'franz-josef' ); ?></option>
                <option value="6" <?php selected( 6, $width ); ?>><?php _e( 'Half width', 'franz-josef' ); ?></option>
                <option value="4" <?php selected( 4, $width ); ?>><?php _e( '1/3 width', 'franz-josef' ); ?></option>
                <option value="3" <?php selected( 3, $width ); ?>><?php _e( '1/4 width', 'franz-josef' ); ?></option>
            </select>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'text_align' ); ?>"><?php _e( 'Text alignment:', 'franz-josef' ); ?></label>
            <select id="<?php echo $this->get_field_id( 'text_align' ); ?>" name="<?php echo $this->get_field_name( 'text_align' ); ?>">
            	<option value="left" <?php selected( 'left', $text_align ); ?>><?php _e( 'Left', 'franz-josef' ); ?></option>
                <option value="center" <?php selected( 'center', $text_align ); ?>><?php _e( 'Center', 'franz-josef' ); ?></option>
                <option value="right" <?php selected( 'right', $text_align ); ?>><?php _e( 'Right', 'franz-josef' ); ?></option>
            </select>
        </p>
        
<?php
	}
}


/**
 * Register the widgets
 */
function franz_register_widgets(){
	register_widget( 'Franz_Top_Bar_Text' );
	register_widget( 'Franz_Top_Bar_Social' );
	register_widget( 'Franz_Top_Bar_Menu' );
}
add_action( 'widgets_init', 'franz_register_widgets' );
