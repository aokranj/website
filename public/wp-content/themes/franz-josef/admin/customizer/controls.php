<?php
/**
 * Add the custom controls to the Customizer
 */
function franz_add_customizer_controls( $wp_customize ) {
	/**
	 * Multiple select
	 */
	class Franz_Multiple_Select_Control extends WP_Customize_Control {
		public $type = 'select';
		public $multiple = false;
		
		public function render_content() {
			if ( ! array_key_exists( 'class', $this->input_attrs ) ) $this->input_attrs['class'] = '';
			if ( $this->multiple ) {
				$this->input_attrs['class'] .= ' chzn-select select-multiple';
				$this->input_attrs['class'] = trim( $this->input_attrs['class'] );
				$this->input_attrs['multiple'] = 'multiple';
			}
			?>
			<label>
				<?php if ( ! empty( $this->label ) ) : ?>
                    <span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
                <?php endif;
                if ( ! empty( $this->description ) ) : ?>
                    <span class="description customize-control-description"><?php echo $this->description; ?></span>
                <?php endif; ?>

                <select <?php $this->link(); ?> <?php $this->input_attrs(); ?>>
                    <?php
                    foreach ( $this->choices as $value => $label ){
						$selected = ( in_array( $value, (array) $this->value() ) ) ? 'selected="selected"' : '';
                        echo '<option value="' . esc_attr( $value ) . '"' . $selected . '>' . $label . '</option>';
					}
                    ?>
                </select>
            </label>
			<?php
		}
	}
	
	/**
	 * Custom text field control
	 */
	class Franz_Enhanced_Text_Control extends WP_Customize_Control {
		public $unit = '';
	 
		public function render_content() {
			?>
			<label class="franz-text">
				<?php if ( ! empty( $this->label ) ) : ?>
					<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
				<?php endif;
				if ( ! empty( $this->description ) ) : ?>
					<span class="description customize-control-description"><?php echo $this->description; ?></span>
				<?php endif; ?>
				<input type="<?php echo esc_attr( $this->type ); ?>" <?php $this->input_attrs(); ?> value="<?php echo esc_attr( $this->value() ); ?>" <?php $this->link(); ?> />
                <?php if ( ! empty( $this->unit ) ) echo $this->unit; ?>
			</label>
			<?php
		}
	}
	
	
	/**
	 * Code textarea control
	 */
	class Franz_Code_Control extends WP_Customize_Control {
		public $mode = 'htmlmixed';
	 
		public function render_content() {
			if ( ! array_key_exists( 'class', $this->input_attrs ) ) $this->input_attrs['class'] = '';
			$this->input_attrs['class'] .= ' widefat code';
			$this->input_attrs['class'] .= trim( $this->input_attrs['class'] );
			
			$matches = array();
			preg_match( '/franz_settings\[(.*)\]/i', $this->id, $matches );
			$setting_name = ( isset( $matches[1] ) ) ? $matches[1] : $this->id;
			?>
			<label class="franz-code">
				<?php if ( ! empty( $this->label ) ) : ?>
					<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
				<?php endif;
				if ( ! empty( $this->description ) ) : ?>
					<span class="description customize-control-description"><?php echo $this->description; ?></span>
				<?php endif; ?>
                
                <textarea id="<?php echo $setting_name; ?>" <?php $this->link(); ?> <?php $this->input_attrs(); ?>><?php echo htmlentities( stripslashes( $this->value() ) ); ?></textarea>
			</label>
            
            <script type="text/javascript">
				var <?php echo $setting_name; ?>CM = CodeMirror.fromTextArea(document.getElementById("<?php echo $setting_name; ?>"), {
					mode			: '<?php echo $this->mode; ?>',
					lineNumbers		: true,
					lineWrapping	: true,
					indentUnit		: 4,
					styleActiveLine	: true,
					autoRefresh		: true
				});
				<?php echo $setting_name; ?>CM.on( 'blur', function(){
					wp.customize( '<?php echo $this->id; ?>', function ( obj ) {
						obj.set( <?php echo $setting_name; ?>CM.getValue() );
					} );
				});
			</script>
			<?php
		}
	}
}