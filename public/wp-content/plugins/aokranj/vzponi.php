<?php
/**
* Seznam vzponov
*
* @package AO Kranj Plugin
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


// only possible pages are aokranj-vzponi and aokranj-vsi-vzponi
$pages = array('aokranj-vzponi', 'aokranj-vsi-vzponi');
$page = filter_input(INPUT_GET, 'page');
if (!in_array($page, $pages)) {
    die('Fuck off!');
}

// ze url of zis page
$url = admin_url('admin.php?page=' . $page);

// handle post filtering wordpress style
// stupid wordpress ...
$action = filter_input(INPUT_POST, 'action');
if ($action === 'filter') {
    $s = filter_input(INPUT_POST, 's');
    $year = filter_input(INPUT_POST, 'year');
    $tip = filter_input(INPUT_POST, 'tip');
    $paged = filter_input(INPUT_POST, 'paged');
    $orderby = filter_input(INPUT_GET, 'orderby');
    $order = filter_input(INPUT_GET, 'order');
    if (!empty($s)) $url .= '&s=' . $s;
    if (!empty($year)) $url .= '&year=' . $year;
    if (!empty($tip)) $url .= '&tip=' . $tip;
    if (!empty($paged)) $url .= '&paged=' . $paged;
    if (!empty($orderby)) $url .= '&orderby=' . $orderby;
    if (!empty($order)) $url .= '&order=' . $order;
    wp_redirect($url);
    die;
}

// stupid wordpress ...
$orderby = filter_input(INPUT_GET, 'orderby');
$order = filter_input(INPUT_GET, 'order');
if (!empty($orderby)) $url .= '&orderby=' . $orderby;
if (!empty($order)) $url .= '&order=' . $order;
// this will disable wordpress header and allow php header redirect with wp_redirect above
$url .= '&noheader=true';

// vzponi table instance
require_once AOKRANJ_PLUGIN_DIR . '/admin/class-vzpon.php';
require_once AOKRANJ_PLUGIN_DIR . '/admin/class-vzponi-list-table.php';
$table = new AOKranj_Vzponi_List_Table();
$table->prepare_items();

?>
<div id="vzponi" class="wrap">
    <h1>
        <?php if ($page === 'aokranj-vsi-vzponi'): ?>
            <?= __('AO Kranj vzponi vseh članov') ?>
        <?php else: ?>
            <?= __('Seznam vzponov') ?>
            <a href="<?= admin_url('/admin.php?page=aokranj-vzpon') ?>" class="page-title-action"><?= __('Dodaj vzpon') ?></a>
        <?php endif; ?>
    </h1>
    <form id="vzponi-filter" action="<?= $url ?>" method="post">
        <input type="hidden" name="page" value="<?= $page ?>" />
        <input type="hidden" name="action" value="filter" />
        <?php $table->search_box(__('Išči vzpone'), 'vzpon'); ?>
        <?php $table->display(); ?>
    </form>
</div>
