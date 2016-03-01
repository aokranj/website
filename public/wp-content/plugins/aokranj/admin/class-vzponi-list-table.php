<?php

/**
 * Tabela vzponov
 */
class AOKranj_Vzponi_List_Table extends WP_List_Table
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
        $start = ($page-1) * $limit;

        $s = filter_input(INPUT_GET, 's');
        if (!empty($s)) {
            //$search = '';
            $search = 'AND (destinacija LIKE \'%' . $s . '%\' OR smer LIKE \'%' . $s . '%\')';
        } else {
            $search = '';
        }

        $query = sprintf('
            SELECT *
            FROM %s
            WHERE user_id = %d %s
            AND (deleted = 0 OR deleted IS NULL)
            ORDER BY %s %s
            LIMIT %d, %d',
            AOKRANJ_TABLE_VZPONI,
            get_current_user_id(),
            $search,
            $sort['property'],
            $sort['direction'],
            $start,
            $limit
        );

        $vzponi = $wpdb->get_results($query, ARRAY_A);

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
            'akcije'      => '',
        );
        return $columns;
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
        $url = admin_url('admin.php?page=aokranj-vzpon&id=' . $item['id']);
        $text = $item['destinacija'];
        $span = '';
        if (!empty($item['smer'])) {
            $span .= ' - ' . $item['smer'];
        }
        if (!empty($item['ocena'])) {
            $span .= ' (' . $item['ocena'] . ')';
        }
        return '<a href="' . $url . '">' . $text . '<span>' . $span . '</span></a>';
    }

    /**
     * Akcije column
     * @param  array $item
     * @return string
     */
    public function column_akcije($item) {
        $url = admin_url('admin.php?page=aokranj-vzpon&id=' . $item['id'] . '&action=delete&noheader=true');
        return '<a href="' . $url . '">' . __('odstrani') . '</a>';
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
            'datum'       => array('datum', true),
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
