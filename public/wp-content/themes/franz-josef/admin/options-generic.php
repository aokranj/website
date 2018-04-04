<?php
function franz_options_generic( $tab ) { global $franz_settings; ?>
    
    <input type="hidden" name="franz_generic" value="true" />    
    <input type="hidden" name="franz_tab" value="<?php echo $tab; ?>" />
    <?php do_action( 'franz_options_' . $tab ); ?>          
        
<?php } // Closes the franz_options_display() function definition 