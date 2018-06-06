<?php

/**
* AOKranjTheme class
*
* @author Bojan Hribernik <bojan.hribernik@gmail.com>
* @copyright AO Kranj
*/
class AOKranjTheme {

  private static $detect = null;

  public function __construct() {
    // start session
    if (!session_id()) {
      session_start();
    }

    // actions
    add_action('after_setup_theme', [$this, 'after_setup_theme']);
    add_action('init', [$this, 'init']);
    add_action('widgets_init', [$this, 'widgets_init']);
    add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);

    // filters
    add_filter('option_use_smilies', [$this, 'option_use_smilies']);
    add_filter('excerpt_more', [$this, 'excerpt_more']);
    add_filter('wp_trim_excerpt', [$this, 'trim_excerpt']);
    add_filter('get_the_archive_title', [$this, 'get_the_archive_title']);
  }

  // actions

  // setup theme
  public function after_setup_theme() {
    // Load translations
    $path = get_template_directory() . '/languages';
    $result = load_theme_textdomain('aokranj', $path);
    if (!$result) {
      $locale = apply_filters('theme_locale', get_locale(),'my_theme');
      die( "Could not find $path/$locale.mo." );
    }

    // Add default posts and comments RSS feed links to head.
    //add_theme_support('automatic-feed-links');

    // Let WordPress manage the document title.
    add_theme_support('title-tag');

    // Remove admin bar
    add_theme_support('admin-bar', ['callback' => '__return_false']);

    // This theme uses wp_nav_menu() in one location.
    register_nav_menus([
      'primary' => __('Primary Menu', 'aokranj'),
    ]);

    // Add HTML5 support
    add_theme_support('html5', [
      'search-form',
      'comment-form',
      'comment-list',
      'gallery',
      'caption',
    ]);

    // Adding Thumbnail basic support
    add_theme_support('post-thumbnails');

    // Adding support for Widget edit icons in customizer
    add_theme_support('customize-selective-refresh-widgets');

    // Enable support for Post Formats.
    add_theme_support('post-formats', [
      'aside',
      'image',
      'video',
      'quote',
      'link',
    ]);

    // Set up the WordPress core custom background feature.
    add_theme_support('custom-background', apply_filters('aokranj_custom_background_args', [
      'default-color' => 'ffffff',
      'default-image' => '',
    ]));

    // Set up the WordPress Theme logo feature.
    add_theme_support('custom-logo');
  }

  // remove emojis and oembed
  public function init() {
    $this->remove_emojis();
    $this->remove_oembed();
  }

  // add scripts and styles
  public function enqueue_scripts() {
    $version = wp_get_theme()->get('Version');
    wp_deregister_script('jquery');
    if (WP_DEBUG) {
      wp_enqueue_script('aokranj', AOKRANJ_URL.'/public/aokranj.js', [], $version, true);
      wp_enqueue_style('aokranj', AOKRANJ_URL.'/public/aokranj.css', [], $version);
    } else {
      wp_enqueue_style('aokranj', AOKRANJ_URL.'/public/aokranj.min.css', [], $version);
      wp_enqueue_script('aokranj', AOKRANJ_URL.'/public/aokranj.min.js', [], $version, true);
    }
  }

  // register footer widget areas
  public function widgets_init() {
    register_sidebar([
      'name'          => __('Sidebar', 'aokranj'),
      'id'            => 'sidebar',
      'description'   => 'Widget area next to main content',
      'before_widget' => '<div id="%1$s" class="%2$s">',
      'after_widget'  => '</div>',
      'before_title'  => '<h4 class="sidebar-title">',
      'after_title'   => '</h4>',
    ]);
    register_sidebar([
      'name'          => __('Footer Informacije', 'aokranj'),
      'id'            => 'footer-info',
      'description'   => 'Widget area below main content',
      'before_widget' => '<div id="%1$s" class="%2$s">',
      'after_widget'  => '</div>',
      'before_title'  => '<h4 class="footer-title">',
      'after_title'   => '</h4>',
    ]);
    register_sidebar([
      'name'          => __('Footer Navigacija', 'aokranj'),
      'id'            => 'footer-nav',
      'description'   => 'Widget area below main content',
      'before_widget' => '<div id="%1$s" class="%2$s col-12 col-sm-6 col-md-6 col-lg-3">',
      'after_widget'  => '</div>',
      'before_title'  => '<h4 class="footer-title">',
      'after_title'   => '</h4>',
    ]);
  }

  // functions

  // remove all actions related to emojis
  public function remove_emojis() {
    remove_action('admin_print_styles', 'print_emoji_styles');
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
    remove_filter('the_content_feed', 'wp_staticize_emoji');
    remove_filter('comment_text_rss', 'wp_staticize_emoji');

    // remove tinymce emojis
    add_filter('tiny_mce_plugins', function($plugins) {
      if (is_array($plugins)) {
        return array_diff($plugins, ['wpemoji']);
      } else {
        return [];
      }
    });
  }

  // remove all actions related to oembed
  public function remove_oembed() {
    remove_action('rest_api_init', 'wp_oembed_register_route');
    remove_filter('oembed_dataparse', 'wp_filter_oembed_result', 10);
    remove_action('wp_head', 'wp_oembed_add_discovery_links');
    remove_action('wp_head', 'wp_oembed_add_host_js');
  }


  // filters

  // disable smileys
  public function option_use_smilies() {
    return false;
  }

  // remove Arhivi: from archive title
  public function get_the_archive_title($title) {
    $index = strpos($title, ':');
    if ($index) {
      return substr($title, $index + 2, strlen($title));
    } else {
      return $title;
    }
  }

  // excerpt more text
  public function excerpt_more($more) {
    return '';
  }

  // format excerpt
  public function trim_excerpt($excerpt) {
    return $excerpt.'<p><a class="read-more-link" href="'.get_permalink(get_the_ID()).'">'.__('Preberi več &gt;', 'aokranj').'</a></p>';
  }

}

$GLOBALS['AOKranjTheme'] = new AOKranjTheme();
