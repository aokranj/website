<?php

// login styles
add_action('login_enqueue_scripts', function(){
  $the_theme = wp_get_theme();
  $version = $the_theme->get('Version');
  $min = WP_DEBUG ? '' : 'min.';
  wp_deregister_script('jquery');
  wp_enqueue_style('aokranj', AOKRANJ_URL.'/public/aokranj.'.$min.'css', [], $version);
  wp_enqueue_script('aokranj', AOKRANJ_URL.'/public/aokranj.'.$min.'js', [], $version, true);
}, 11);

// login header url
add_filter('login_headerurl', function(){
  return site_url();
});

// login header title
add_filter('login_headertitle', function(){
  return get_bloginfo('name');
});
