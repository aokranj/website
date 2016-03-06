<?php
/**
* Prenos podatkov
*
* @package AO Kranj Plugin
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$response = isset($_SESSION['prenos']) && is_array($_SESSION['prenos']) ? $_SESSION['prenos'] : false;
unset($_SESSION['prenos']);
?>

<div class="wrap">
    <h1><?= __('Prenos podatkov je uspel :)') ?></h1>
    <?php /*
    <br />
    <?php if ($response): ?>
        <pre><?= print_r($response, true) ?></pre>
    <?php else: ?>
        <form id="prenos" action="<?= admin_url('admin-post.php') ?>" method="post">
            <?php wp_nonce_field('prenos_podatkov') ?>
            <input type="hidden" name="action" value="prenos_podatkov" />
            <input type="hidden" name="data" value="prenos" />
            <input class="button button-primary" type="submit" value="<?= __('Prenesi podatke') ?>" />
        </form>
    <?php endif; ?>
    */ ?>
</div>
