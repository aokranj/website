<?php

function dump($data, $simple = true) {
  if (WP_DEBUG) {
    echo '<pre class="small border bg-light-gray p-3">';
    $simple ? print_r($data) : var_dump($data);
    echo '</pre>';
  }
}

function has_featured_image() {
  return has_post_thumbnail() && !is_home() && !is_archive();
}

function get_page_url($slug) {
  $page = get_page_by_path($slug);
  return $page ? get_permalink(pll_get_post($page->ID)) : '/';
}

function get_post_meta_value($id, $key, $default = null) {
  $value = get_post_meta($id, $key, true);
  return empty($value) ? $default : $value;
}

function get_meta($key, $default = null) {
	$value = get_post_meta(get_the_ID(), $key, true);
  return empty($value) ? $default : $value;
}

function get_photo($photo, $alt = '') {
	$srcset = [
		AOKRANJ_URL.'/images/photos/'.$photo.' 1920w',
		AOKRANJ_URL.'/images/photos/medium/'.$photo.' 1280w',
		AOKRANJ_URL.'/images/photos/small/'.$photo.' 768w',
	];
	return '
	<img
		class="img-fluid w-100"
		src="'.$srcset[0].'"
		srcset="'.implode(',', $srcset).'"
		alt="'.$alt.'"
	/>';
}

function format_price($price) {
	return empty($price) ? 0 : $price;
}

function get_price($price) {
	return '<span class="price">'.format_price($price).'</span> €';
}

function aokranj_post_nav() {
  // Don't print empty markup if there's nowhere to navigate.
  $previous = is_attachment()
    ? get_post(get_post()->post_parent)
    : get_adjacent_post(false, '', true);
  $next = get_adjacent_post(false, '', false);

  if (!$previous && !$next) {
    return;
  }
  ?>
  <nav class="container navigation post-navigation">
    <h2 class="sr-only"><?php _e('Navigacija', 'aokranj'); ?></h2>
    <div class="row nav-links justify-content-between">
      <?php
      if (get_previous_post_link()) {
        previous_post_link(
					'<span class="nav-previous">%link</span>', 
					_x('<i class="fa fa-angle-left"></i>&nbsp;%title', 'Prejšnja objava', 'aokranj')
				);
      }
      if (get_next_post_link()) {
        next_post_link(
					'<span class="nav-next">%link</span>',
					_x('%title&nbsp;<i class="fa fa-angle-right"></i>', 'Naslednja objava', 'aokranj')
				);
      }
      ?>
    </div><!-- .nav-links -->
  </nav><!-- .navigation -->
  <?php
}

function aokranj_posted_on() {
	$time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
	if (get_the_time('U') !== get_the_modified_time('U')) {
		$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s"> (%4$s) </time>';
	}
	$time_string = sprintf($time_string,
		esc_attr(get_the_date('c')),
		esc_html(get_the_date()),
		esc_attr(get_the_modified_date('c')),
		esc_html(get_the_modified_date())
	);
	$posted_on = sprintf(
		esc_html_x('Objavljeno %s', 'post date', 'aokranj'),
		'<a href="'.esc_url(get_permalink()).'" rel="bookmark">'.$time_string.'</a>'
	);
	$byline = sprintf(
		esc_html_x('objavil %s', 'avtor', 'aokranj'),
		'<span class="author vcard"><a class="url fn n" href="'.esc_url(get_author_posts_url(get_the_author_meta('ID'))).'">'.esc_html(get_the_author()).'</a></span>'
	);
	echo '<span class="posted-on">'.$posted_on.'</span><span class="byline"> '.$byline.'</span>'; // WPCS: XSS OK.
}

function aokranj_entry_footer() {
  /*
	if ('post' === get_post_type()) {
		$categories_list = get_the_category_list(esc_html__(', ', 'aokranj'));
		if ($categories_list) {
			printf('<span class="cat-links">'.esc_html__('Objavljeno v %1$s', 'aokranj').'</span>', $categories_list); // WPCS: XSS OK.
		}
		$tags_list = get_the_tag_list('', esc_html__(', ', 'aokranj'));
		if ($tags_list) {
			printf('<span class="tags-links">'.esc_html__('Označeno %1$s', 'aokranj').'</span>', $tags_list); // WPCS: XSS OK.
		}
	}
	if (!is_single() && !post_password_required() && (comments_open() || get_comments_number())) {
		echo '<span class="comments-link">';
		comments_popup_link(esc_html__('Objavite komentar', 'aokranj'), esc_html__('1 komentar', 'aokranj'), esc_html__('% komentarjev', 'aokranj'));
		echo '</span>';
	}
  */
	edit_post_link(
		sprintf(
			esc_html__('Uredi %s', 'aokranj'),
			the_title('<span class="screen-reader-text">"', '"</span>', false)
		),
		'<span class="edit-link">',
		'</span>'
	);
}

function aokranj_pagination() {
  // http://www.wpbeginner.com/wp-themes/how-to-add-numeric-pagination-in-your-wordpress-theme/
	if (is_singular()) {
		return;
	}

	global $wp_query;

	/** Stop execution if there's only 1 page */
	if ($wp_query->max_num_pages <= 1) {
		return;
	}

	$paged = get_query_var('paged') ? absint(get_query_var('paged')) : 1;
	$max   = intval($wp_query->max_num_pages);

	/**    Add current page to the array */
	if ($paged >= 1) {
		$links[] = $paged;
	}

	/**    Add the pages around the current page to the array */
	if ($paged >= 3) {
		$links[] = $paged - 1;
		$links[] = $paged - 2;
	}

	if (($paged + 2) <= $max) {
		$links[] = $paged + 2;
		$links[] = $paged + 1;
	}

	echo '<nav aria-label="Page navigation"><ul class="pagination ">'."\n";

	/**    Link to first page, plus ellipses if necessary */
	if (! in_array(1, $links)) {
		$class = 1 == $paged ? ' class="active page-item"' : ' class="page-item"';

		printf('<li %s><a class="page-link" href="%s"><i class="fa fa-step-backward" aria-hidden="true"></i></a></li>'."\n",
		$class, esc_url(get_pagenum_link(1)), '1');

		/**    Previous Post Link */
		if (get_previous_posts_link()) {
			printf('<li class="page-item page-item-direction page-item-prev"><span class="page-link">%1$s</span></li> '."\n",
			get_previous_posts_link('<span aria-hidden="true">&laquo;</span><span class="sr-only">Previous page</span>'));
		}

		if (! in_array(2, $links)) {
			echo '<li class="page-item"></li>';
		}
	}

	// Link to current page, plus 2 pages in either direction if necessary.
	sort($links);
	foreach ((array) $links as $link) {
		$class = $paged == $link ? ' class="active page-item"' : ' class="page-item"';
		printf('<li %s><a href="%s" class="page-link">%s</a></li>'."\n", $class,
			esc_url(get_pagenum_link($link)), $link);
	}

	// Next Post Link.
	if (get_next_posts_link()) {
		printf('<li class="page-item page-item-direction page-item-next"><span class="page-link">%s</span></li>'."\n",
			get_next_posts_link('<span aria-hidden="true">&raquo;</span><span class="sr-only">Next page</span>'));
	}

	// Link to last page, plus ellipses if necessary.
	if (! in_array($max, $links)) {
		if (! in_array($max - 1, $links)) {
			echo '<li class="page-item"></li>'."\n";
		}

		$class = $paged == $max ? ' class="active "' : ' class="page-item"';
		printf('<li %s><a class="page-link" href="%s" aria-label="Next"><span aria-hidden="true"><i class="fa fa-step-forward" aria-hidden="true"></i></span><span class="sr-only">%s</span></a></li>'."\n",
		$class.'', esc_url(get_pagenum_link(esc_html($max))), esc_html($max));
	}

	echo '</ul></nav>'."\n";
}
