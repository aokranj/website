<?php
$utrinki = new WP_Query([
  'category_name' => 'utrinki',
  'posts_per_page' => 10,
]);
?>
<section class="zadnji-utrinki" id="zadnji-utrinki">
  <div class="container">
    <div class="row">
      <div class="col-12">
        <h1 class="mb-5">
          <?= __('Utrinki', 'aokranj'); ?>
        </h1>
      </div>
      <div class="col-12">
        <ul class="list-unstyled">
          <?php while ($utrinki->have_posts()): $utrinki->the_post(); ?>
            <li><a href="<?= the_permalink(); ?>"><?= the_title(); ?></a></li>
          <?php endwhile; ?>
        </ul>
      </div>
    </div>
  </div>
</section>
