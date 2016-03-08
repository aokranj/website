<?php

/**
 * Tabela vzponov
 */
class AOKranj_Vzponi_List_Table extends WP_List_Table
{
    /**
     * Show all ascents from all users
     *
     * Only available for editors and administrators
     *
     * @var bool
     */
    protected $showAll = false;

    /**
     * Constructor
     */
    public function __construct() {
        global $status, $page;

        parent::__construct(array(
            'singular' => __('vzpon'),
            'plural' => __('vzponi'),
        ));

        // show all?
        // only available for editors and administrators
        $showAll = filter_input(INPUT_GET, 'page') === 'aokranj-vsi-vzponi';
        if ($showAll && current_user_can('edit_pages')) {
            $this->showAll = true;
        }
    }

    /**
     * Prepare items for the table to process
     */
    public function prepare_items() {
        global $wpdb;

        // select statement
        // if show all is true, we will join user table
        $select = ['vzpon.*'];
        if ($this->showAll) {
            $select[] = 'user.display_name AS user';
        }

        // build WHERE statements
        $selectWhere = [];
        $countWhere = [];

        // only select posts for current user
        // unless show all is true and user can edit pages (is editor)
        if (!$this->showAll) {
            $condition = 'user_id = ' . (int)get_current_user_id();
            $selectWhere[] = $condition;
            $countWhere[] = $condition;
        }

        // only select non-deleted posts
        $notDeleted = '(deleted = 0 OR deleted IS NULL)';
        $selectWhere[] = $notDeleted;
        $countWhere[] = $notDeleted;

        // search filter
        // test destinacija and smer
        $s = filter_input(INPUT_GET, 's');
        if (!empty($s)) {
            $str = '%' . $s . '%';
            $selectWhere[] = $wpdb->prepare('(destinacija LIKE %s OR smer LIKE %s)', $str, $str);
        }

        // year
        $year = filter_input(INPUT_GET, 'year');
        if (!empty($year)) {
            $condition = $wpdb->prepare('DATE_FORMAT(datum, "%%Y") = %d', $year);
            $selectWhere[] = $condition;
            $countWhere[] = $condition;
        }

        // year
        $tip = filter_input(INPUT_GET, 'tip');
        if (!empty($tip) && isset(AOKranj_Vzpon::$tipi[$tip])) {
            $condition = $wpdb->prepare('tip = %s', $tip);
            $selectWhere[] = $condition;
            $countWhere[] = $condition;
        }

        // join statement
        // only join when showing all
        $join = '';
        if ($this->showAll) {
            $join = sprintf('INNER JOIN %s user ON user.ID = vzpon.user_id', $wpdb->users);
        }

        // pagination
        $limit = 20;
        $sort = $this->get_sort();
        $page = $this->get_pagenum();
        $start = ($page-1) * $limit;

        $select = implode(', ', $select);
        $selectWhere = implode(' AND ', $selectWhere);

        // build query
        $query = sprintf('
            SELECT %s FROM %s vzpon %s WHERE %s ORDER BY %s %s LIMIT %d, %d',
            $select,
            AOKRANJ_TABLE_VZPONI,
            $join,
            $selectWhere,
            $sort['property'],
            $sort['direction'],
            $start,
            $limit
        );

        // select vponi
        $vzponi = $wpdb->get_results($query, ARRAY_A);

        // select total
        $total = $wpdb->get_var(sprintf('
            SELECT COUNT(id)
            FROM %s
            WHERE %s',
            AOKRANJ_TABLE_VZPONI,
            implode(' AND ', $countWhere)
        ));

        // set pagination
        $this->set_pagination_args(array(
            'total_items' => $total,
            'per_page'    => $limit
        ));

        // set column headers
        $this->_column_headers = array(
            $this->get_columns(),
            $this->get_hidden_columns(),
            $this->get_sortable_columns()
        );

        // set items
        $this->items = $vzponi;
    }

    /**
     * Override the parent columns method. Defines the columns to use in your listing table
     *
     * @return Array
     */
    public function get_columns() {
        $columns = array();

        if ($this->showAll) {
            $columns['user'] = __('Uporabnik');
        }

        $columns = array_merge($columns, array(
            'destinacija' => __('Destinacija'),
            'smer'        => __('Smer'),
            'ocena'       => __('Ocena'),
            'tip'         => __('Tip'),
            'partner'     => __('Soplezalec'),
            'datum'       => __('Datum'),
        ));

        if (!$this->showAll) {
            $columns['akcije'] = __('Akcije');
        }

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
     * Override user column
     *
     * @param  array $item
     * @return string
     */
    public function column_user($item) {
        $text = $item['user'];
        $span = '';
        if (!empty($item['destinacija'])) {
            $span .= ' - ' . $item['destinacija'];
        }
        if (!empty($item['smer'])) {
            $span .= ' - ' . $item['smer'];
        }
        if (!empty($item['ocena'])) {
            $span .= ' (' . $item['ocena'] . ')';
        }
        return $text . ' <span>' . $span . '</span>';
    }

    /**
     * Make destination clickable
     *
     * @param  array $item
     * @return string
     */
    public function column_destinacija($item) {
        if ($this->showAll) {
            return $item['destinacija'];
        }
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
     * Show tip
     *
     * @param  array $item
     * @return string
     */
    public function column_tip($item) {
        $tipi = AOKranj_Vzpon::$tipi;
        return isset($tipi[$item['tip']]) ? $tipi[$item['tip']] : '';
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
        $columns = array(
            'destinacija' => array('destinacija', false),
            'smer'        => array('smer', false),
            'ocena'       => array('ocena', false),
            'partner'     => array('partner', false),
            'datum'       => array('datum', true),
        );
        if ($this->showAll) {
            $columns['user'] = array('user', false);
        }
        return $columns;
    }

    /**
     * Get sort
     *
     * @return array
     */
    protected function get_sort() {
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
        if ($this->showAll) {
            $properties[] = 'user';
        }

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

        if ($property === 'user') {
            $property = 'user.display_name';
        }

        return array(
            'property' => $property,
            'direction' => $direction,
        );
    }

    /**
     * Extra table filters and navigation
     * @param  string $which top|bottom
     */
    protected function extra_tablenav( $which ) {
		global $cat;

        $output = ['<div class="alignleft actions">'];

		if ( 'top' === $which && !is_singular() ) {

            // build year dropdown
            $output[] = $this->extra_year_filter();

            // build tip dropdown
            $output[] = $this->extra_tip_filter();

            // filter button
            $output[] = '<input type="submit" name="filter" class="button" value="' . __('Filtriraj') . '">';
        }

        $output[] = '</div>';

        echo implode('', $output);
    }

    /**
     * Tip filter
     *
     * @return string
     */
    protected function extra_tip_filter() {
        $tipi = AOKranj_Vzpon::$tipi;

        // current tip
        $tip = filter_input(INPUT_GET, 'tip');

        // label
        $output[] = '<label style="font-size:14px;padding-right:5px;" for="year">' . __( 'Tip vzpona' ) . '</label>';

        // select box
        $output[] = '<select name="tip" id="tip" style="float:none;">';

        // empty option
        $emptySelected = empty($tip) ? ' selected' : '';
        $output[] = sprintf('<option value=""%s>%s</option>', $emptySelected, __('Vsi tipi'));

        // options
        foreach ($tipi as $value => $label) {
            $selected = $value === $tip ? ' selected' : '';
            $output[] = sprintf('<option value="%1$s"%3$s>%2$s</option>', $value, __($label), $selected);
        }

        // close select
        $output[] = '</select>';

        return implode('', $output);
    }

    /**
     * Year filter
     *
     * @return string
     */
    protected function extra_year_filter() {
        $output = [];

        // current year
        $year = filter_input(INPUT_GET, 'year');

        // fetch years
        global $wpdb;
        $query = '
            SELECT DISTINCT DATE_FORMAT(datum, \'%Y\') AS year
            FROM ' . AOKRANJ_TABLE_VZPONI . '
            WHERE DATE_FORMAT(datum, "%Y") > 1900
            ORDER BY datum DESC
        ';
        $years = $wpdb->get_results($query, ARRAY_A);
        $years = array_map(function($y){ return $y['year']; }, $years);

        // label
        $output[] = '<label style="font-size:14px;padding-right:5px;" for="year">' . __( 'Leto' ) . '</label>';

        // select box
        $output[] = '<select name="year" id="year" style="float:none;">';

        // empty option
        $emptySelected = empty($year) ? ' selected' : '';
        $output[] = sprintf('<option value=""%s>%s</option>', $emptySelected, __('Vsa leta'));

        // options
        foreach ($years as $y) {
            $selected = $y === $year ? ' selected' : '';
            $output[] = sprintf('<option value="%1$d"%2$s>%1$d</option>', $y, $selected);
        }

        // close select
        $output[] = '</select>';

        return implode('', $output);
    }

}
