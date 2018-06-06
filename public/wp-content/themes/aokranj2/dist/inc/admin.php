<?php

/**
 * AOKranj admin class
 *
 * @author Bojan Hribernik <bojan.hribernik@gmail.com>
 * @copyright AO Kranj
 */
class AOKranjAdmin {

  public function __construct() {
    // actions
    add_action('admin_init', [$this, 'init']);
    add_action('admin_head', [$this, 'admin_head']);
    add_action('upload_mimes', [$this, 'upload_mimes']);

    // filters
    add_filter('tiny_mce_before_init', [$this, 'tiny_mce_before_init']);
    add_filter('mce_buttons_2', [$this, 'mce_buttons_2']);
  }


  // actions

  public function init() {
    remove_editor_styles();
    add_editor_style(AOKRANJ_URL.'/public/aokranj.css');
  }

  // admin styles
  public function admin_head() {
    echo '
    <style type="text/css">
      #adminmenu, #adminmenu .wp-submenu, #adminmenuback, #adminmenuwrap {
        width: 185px;
      }
      #adminmenu .wp-submenu {
        left: 185px;
      }
      #wpcontent, #wpfooter {
        margin-left: 185px;
      }
      #excerpt {
        height: 150px;
      }
      td.media-icon img[src$=".svg"] {
        width: 100% !important;
        height: auto !important;
      }
      #acf-post_thumbnail_content p.label {
        display: none;
      }
    </style>';
  }

  // add svg support
  public function upload_mimes($file_types) {
    return array_merge($file_types, ['svg' => 'image/svg+xml']);
  }


  // filters

  // editor <span> fix and style formats
  public function tiny_mce_before_init($settings) {
    $valid_elements = '*[*]';
    $settings['valid_elements'] = $valid_elements;
    $settings['extended_valid_elements'] = $valid_elements;
    $style_formats = [[
      'title' => 'Lead Paragraph',
      'selector' => 'p',
      'classes' => 'lead',
      'wrapper' => true
    ],[
      'title' => 'Blockquote',
      'block' => 'blockquote',
      'classes' => 'blockquote',
      'wrapper' => true
    ],[
      'title' => 'Cite',
      'inline' => 'cite'
    ],[
      'title' => 'Small',
      'inline' => 'small'
    ]];
    if (isset($settings['style_formats'])) {
      $orig_style_formats = json_decode($settings['style_formats'],true);
      $style_formats = array_merge($orig_style_formats,$style_formats);
    }
    $settings['style_formats'] = json_encode($style_formats);
    return $settings;
  }

  // editor style select
  public function mce_buttons_2($styles) {
    array_unshift($styles, 'styleselect');
    return $styles;
  }

}

$GLOBALS['AOKranjAdmin'] = new AOKranjAdmin();
