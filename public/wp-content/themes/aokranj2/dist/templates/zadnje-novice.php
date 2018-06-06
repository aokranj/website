<?php
$arhiv = get_category_by_slug('arhiv-novic');
$novice = new WP_Query([
  'category_name' => 'novice',
  'category__not_in' => !empty($arhiv) ? [$arhiv->cat_ID] : [],
  'posts_per_page' => 5,
]);
?>
<section class="zadnje-novice" id="zadnje-novice">
  <div class="container">
    <div class="row">
      <div class="col-12">
        <h1 class="mb-5">
          <?= __('Novice', 'aokranj'); ?>
        </h1>
      </div>
      <div class="col-12">
        <ul class="list-unstyled">
          <?php while ($novice->have_posts()): $novice->the_post(); ?>
            <li><a href="<?= the_permalink(); ?>"><?= the_title(); ?></a></li>
          <?php endwhile; ?>
        </ul>
      </div>
    </div>
  </div>
</section>
