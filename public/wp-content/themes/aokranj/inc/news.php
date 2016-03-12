<?php

/**
 * Show news on frontpage top
 */
function aokranj_front_page_top_news() {
    $novice = get_category_by_slug('novice');
    $arhiv = get_category_by_slug('arhiv-novic');
    $news = query_posts(array(
        'category_name' => 'novice',
        'category__not_in' => array($arhiv->cat_ID),
        'posts_per_page' => -1
    ));
    if (!have_posts()) return;
    $news_category_link = get_category_link($novice->cat_ID);
    ?>
    <div class="news-list">
        <h2><?php echo __('Novice'); ?></h2>
        <ul>
            <?php while (have_posts()): the_post();?>
                <li>
                    <span><?php the_time( 'd.m.Y' ) ?> - </span>
                    <h3>
                        <a class="link"
                           href="<?php the_permalink() ?>"
                           rel="bookmark"
                           title="<?php the_title_attribute(); ?>">
                            <?php the_title(); ?>
                        </a>
                    </h3>
                </li>
            <?php endwhile; ?>
        </ul>
        <a href="<?php echo esc_url( $news_category_link ); ?>" class="btn btn-lg btn-warning btn-outline btn-sharp">Več novic</a>
    </div>
    <?php
    wp_reset_query();
}

add_action('franz_front_page_top', 'aokranj_front_page_top_news');
