<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after
 *
 * @package aokranj
 */
?>

<!-- footer -->
<footer class="section aokranj-footer">
  <div class="container">
    <div class="row">
      <div class="footer-info-widget col-md-5 col-lg-4">
        <?php dynamic_sidebar('footer-info'); ?>
      </div>
      <div class="col-md-7 col-lg-8">
        <div class="footer-nav-widget row">
          <?php dynamic_sidebar('footer-nav'); ?>
        </div>
      </div>
    </div>
  </div>
</footer>

<a class="go-top" href="#">
  <svg version="1.1" xmlns="http://www.w3.org/2000/svg" width="18" height="28" viewBox="0 0 18 28">
    <path d="M16.797 18.5c0 0.125-0.063 0.266-0.156 0.359l-0.781 0.781c-0.094 0.094-0.219 0.156-0.359 0.156-0.125 0-0.266-0.063-0.359-0.156l-6.141-6.141-6.141 6.141c-0.094 0.094-0.234 0.156-0.359 0.156s-0.266-0.063-0.359-0.156l-0.781-0.781c-0.094-0.094-0.156-0.234-0.156-0.359s0.063-0.266 0.156-0.359l7.281-7.281c0.094-0.094 0.234-0.156 0.359-0.156s0.266 0.063 0.359 0.156l7.281 7.281c0.094 0.094 0.156 0.234 0.156 0.359z"></path>
  </svg>
</a>

<?php wp_footer(); ?>

</body>

</html>
