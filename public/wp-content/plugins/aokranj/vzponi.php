<?php
/**
* Seznam vzponov
*
* @package AO Kranj Plugin
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// stupid wordpress ...
$action = filter_input(INPUT_POST, 'action');
if ($action === 'filter') {
    $url = admin_url('admin.php?page=aokranj-vzponi');
    $s = filter_input(INPUT_POST, 's');
    $paged = filter_input(INPUT_POST, 'paged');
    $orderby = filter_input(INPUT_GET, 'orderby');
    $order = filter_input(INPUT_GET, 'order');
    if (!empty($s)) $url .= '&s=' . $s;
    if (!empty($paged)) $url .= '&paged=' . $paged;
    if (!empty($orderby)) $url .= '&orderby=' . $orderby;
    if (!empty($order)) $url .= '&order=' . $order;
    wp_redirect($url);
    die;
}

// vzponi table instance
require_once AOKRANJ_PLUGIN_DIR . '/admin/class-vzponi-list-table.php';
$table = new AOKranj_Vzponi_List_Table();
$table->prepare_items();

// stupid wordpress ...
$url = admin_url('admin.php?page=aokranj-vzponi');
$orderby = filter_input(INPUT_GET, 'orderby');
$order = filter_input(INPUT_GET, 'order');
if (!empty($orderby)) $url .= '&orderby=' . $orderby;
if (!empty($order)) $url .= '&order=' . $order;

?>
<div id="vzponi" class="wrap">
    <h1>
        <?= __('Seznam vzponov') ?>
        <a href="<?= admin_url('/admin.php?page=aokranj-vzpon') ?>" class="page-title-action"><?= __('Dodaj vzpon') ?></a>
    </h1>

    <form id="vzponi-filter" action="<?= $url . '&noheader=true' ?>" method="post">
        <?php /* <input type="hidden" name="action" value="isci_vzpone" /> */ ?>
        <input type="hidden" name="page" value="aokranj-vzponi" />
        <input type="hidden" name="action" value="filter" />
        <?php $table->search_box(__('Išči vzpone'), 'vzpon'); ?>
        <?php $table->display(); ?>
    </form>
</div>
