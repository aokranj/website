<?php
/**
* Seznam vzponov
*
* @package AO Kranj Plugin
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Vzponi_List_Table extends WP_List_Table
{
    /**
     * Constructor
     */
    public function __construct() {
        global $status, $page;

        parent::__construct(array(
            'singular' => __('vzpon'),
            'plural' => __('vzponi'),
        ));
    }

    /**
     * Prepare the items for the table to process
     *
     * @return Void
     */
    public function prepare_items() {
        global $wpdb;

        $limit = 20;
        $sort = $this->get_sort();
        $page = $this->get_pagenum();
        $start = $page * $limit;

        $vzponi = $wpdb->get_results(sprintf('
            SELECT *
            FROM %s
            WHERE user_id = %d
            ORDER BY %s %s
            LIMIT %d, %d',
            AOKRANJ_TABLE_VZPONI,
            get_current_user_id(),
            $sort['property'],
            $sort['direction'],
            $start,
            $limit
        ), ARRAY_A);

        $total = $wpdb->get_var(sprintf('
            SELECT COUNT(id)
            FROM %s
            WHERE user_id = %d',
            AOKRANJ_TABLE_VZPONI,
            get_current_user_id()
        ));

        $this->set_pagination_args(array(
            'total_items' => $total,
            'per_page'    => $limit
        ));

        $this->_column_headers = array(
            $this->get_columns(),
            $this->get_hidden_columns(),
            $this->get_sortable_columns()
        );

        $this->items = $vzponi;
    }

    /**
     * Default column renderer
     *
     * @param array $item
     * @param string $column_name
     * @return HTML
     */
    public function column_default($item, $column_name) {
        return $item[$column_name];
    }

    /**
     * Make destination clickable
     *
     * @param  array $item
     * @return string
     */
    public function column_destinacija($item) {
        $url = admin_url('admin.php?page=aokranj/vzpon.php&id=' . $item['id']);
        $text = $item['destinacija'];
        /*
        if (!empty($item['smer'])) {
            $text .= ' - ' . $item['smer'];
        }
        if (!empty($item['ocena'])) {
            $text .= ' (' . $item['ocena'] . ')';
        }
        */
        return '<a href="' . $url . '">' . $text . '</a>';
    }

    /**
     * Override the parent columns method. Defines the columns to use in your listing table
     *
     * @return Array
     */
    public function get_columns() {
        $columns = array(
            'destinacija' => __('Destinacija'),
            'smer'        => __('Smer'),
            'ocena'       => __('Ocena'),
            'partner'     => __('Soplezalec'),
            'datum'       => __('Datum'),
        );
        return $columns;
    }

    /**
     * Define which columns are hidden
     *
     * @return Array
     */
    public function get_hidden_columns() {
        return array();
    }

    /**
     * Define the sortable columns
     *
     * @return Array
     */
    public function get_sortable_columns() {
        return array(
            'destinacija' => array('destinacija', false),
            'smer'        => array('smer', false),
            'ocena'       => array('ocena', false),
            'partner'     => array('partner', false),
            'datum'       => array('datum', false),
        );
    }

    /**
     * Get sort
     *
     * @return array
     */
    private function get_sort() {
        $properties = array(
            'destinacija',
            'smer',
            'partner',
            'ocena',
            'datum',
            'tip',
            'cas',
            'visina_smer',
            'visina_izstop',
            'pon_vrsta',
            'pon_nacin',
            'stil',
            'mesto',
            'opomba',
        );
        $directions = array(
            'asc',
            'desc'
        );

        $property = 'datum';
        $direction = 'DESC';

        $orderby = filter_input(INPUT_GET, 'orderby');
        $order = filter_input(INPUT_GET, 'order');

        if (in_array($orderby, $properties)) {
            $property = $orderby;
        }

        if (in_array($order, $directions)) {
            $direction = strtoupper($order);
        }

        return array(
            'property' => $property,
            'direction' => $direction,
        );
    }

}

$table = new Vzponi_List_Table();
$table->prepare_items();

?>
<div id="vzponi" class="wrap">
    <h2><?= __('Seznam vzponov') ?></h2>
    <?php $table->search_box(__('Išči vzpone'), 'vzpon'); ?>
    <?php $table->display(); ?>
</div>
