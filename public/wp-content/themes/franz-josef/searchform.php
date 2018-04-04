<form class="searchform" action="<?php echo esc_url( home_url() ); ?>" method="get" role="form">
	<?php do_action( 'franz_search_form' ); ?>
    <div class="form-group">
        <label for="s" class="sr-only"><?php _e( 'Search keyword', 'franz-josef' ); ?></label>
        <input type="text" name="s" id="s" placeholder="<?php esc_attr_e( 'Search', 'franz-josef' ); ?>" class="form-control" value="<?php echo get_search_query(); ?>" />
        <button type="submit" class="pull-right flip"><i class="fa fa-search"></i></button>
    </div>
</form>