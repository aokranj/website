<?php

/**
 * Show news on frontpage top
 */
function aokranj_front_page_top_news() {
    $news = query_posts(array('category_name' => 'novice','posts_per_page' => -1));
    if (!have_posts()) return;
    ?>
    <div class="posts-list news highlights">
        <h2 class="highlight-title"><?php echo __('Novice'); ?></h2>
        <div class="row items-container">
            <div class="item-wrap col-md-12">
                <div class="item">
                    <?php while (have_posts()): the_post();?>
                        <h3 class="title">
                            <a class="link" href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>">
                                <?php the_title(); ?>
                            </a>
                        </h3>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    </div>
    <?php
    wp_reset_query();
}

add_action('franz_front_page_top', 'aokranj_front_page_top_news');
