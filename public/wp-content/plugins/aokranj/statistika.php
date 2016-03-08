<?php
/**
* Seznam vzponov
*
* @package AO Kranj Plugin
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// vzponi table instance
//require_once AOKRANJ_PLUGIN_DIR . '/admin/class-statistika.php';
//$table = new AOKranj_Vzponi_List_Table();
//$table->prepare_items();

/*
// stupid wordpress ...
$url = admin_url('admin.php?page=aokranj-vzponi');
$orderby = filter_input(INPUT_GET, 'orderby');
$order = filter_input(INPUT_GET, 'order');
if (!empty($orderby)) $url .= '&orderby=' . $orderby;
if (!empty($order)) $url .= '&order=' . $order;
*/

require_once AOKRANJ_PLUGIN_DIR . '/admin/class-vzpon.php';

// fetch stats data
global $wpdb;
$query = '
    SELECT tip, DATE_FORMAT(datum, \'%Y\') AS year, COUNT(*) AS count FROM ' . AOKRANJ_TABLE_VZPONI . '
    WHERE DATE_FORMAT(datum, \'%Y\') > 1900
    GROUP BY tip, DATE_FORMAT(datum, \'%Y\')
    ORDER BY datum DESC
';
$data = $wpdb->get_results($query, ARRAY_A);

// parse stats data
$stats = array();
$sum = array();
foreach ($data as $item) {
    $y = $item['year'];
    $t = $item['tip'];
    if (!isset($stats[$y])) {
        $stats[$y] = array(
            'tipi' => array(),
            'sum' => 0,
        );
    }
    $stats[$y]['tipi'][] = array(
        'tip' => $item['tip'],
        'count' => $item['count'],
    );
    $stats[$y]['sum'] += $item['count'];
    if (!isset($sum[$t])) {
        $sum[$t] = 0;
    }
    $sum[$t] += $item['count'];
}

// show stats data
?>
<div id="statistika" class="wrap">
    <h1>
        <?= __('AO Kranj statistika vzponov') ?>
    </h1>
    <div class="years">
        <div class="year sum">
            <h2>Skupaj</h2>
            <ul>
                <?php foreach ($sum as $tip => $count): ?>
                    <li>
                        <span class="tip"><?= __(AOKranj_Vzpon::$tipi[$tip]) ?>:</span>
                        <span class="count"><?= $count ?> vzponov</span>
                    </li>
                <?php endforeach; ?>
                <li>
                    <span class="tip"><?= __('Vseh skupaj') ?>:</span>
                    <span class="count"><?= array_sum($sum) ?> vzponov</span>
                </li>
            </ul>
        </div>
        <?php foreach ($stats as $year => $data): ?>
            <div class="year">
                <h2><?= $year ?></h2>
                <ul>
                    <?php foreach ($data['tipi'] as $item): ?>
                        <li>
                            <span class="tip"><?= __(AOKranj_Vzpon::$tipi[$item['tip']]) ?>:</span>
                            <span class="count"><?= $item['count'] ?> vzponov</span>
                        </li>
                    <?php endforeach; ?>
                    <li>
                        <span class="tip"><?= __('Skupaj') ?>:</span>
                        <span class="count"><?= $data['sum'] ?> vzponov</span>
                    </li>
                </ul>
            </div>
        <?php endforeach; ?>
    </div>
</div>
