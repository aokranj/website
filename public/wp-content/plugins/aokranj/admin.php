<?php

/**
 * AOKranj administration
 *
 * @package aokranj
 * @link http://aokranj.com/
 * @author Bojan Hribernik <bojan.hribernik@gmail.com>
 */
class AOKranj_Admin extends AOKranj
{
    public function __construct()
    {
        if (is_multisite())
        {
            $options_page = 'settings.php';
            $options_form_action = '../options.php';
            $options_capability = 'manage_network_options';
        }
        else
        {
            $options_page = 'options-general.php';
            $options_form_action = 'options.php';
            $options_capability = 'manage_options';
        }
        
        add_action('admin_enqueue_scripts', array(&$this, 'admin_enqueue_scripts'));
            
        add_action('wp_ajax_vzponi', array(&$this, 'vzponi'));
        add_action('wp_ajax_dodaj_vzpon', array(&$this, 'dodaj_vzpon'));
    }
    
    /**
     * Wordpress hooks
     */

    public function activate()
    {
        global $wpdb;

        if (is_multisite() && !is_network_admin())
        {
            die(self::NAME . ' must be activated via the Network Admin interface'
                    . 'when WordPress is in multistie network mode.');
        }

        /*
         * Create or alter the plugin's tables as needed.
         */

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        // Note: dbDelta() requires two spaces after "PRIMARY KEY".  Weird.
        // WP's insert/prepare/etc don't handle NULL's (at least in 3.3).
        // It also requires the keys to be named and there to be no space
        // the column name and the key length.
        $sql = "CREATE TABLE " . $this->table_vzponi . " (
                id int(10) unsigned NOT NULL AUTO_INCREMENT,
                user_id bigint(20) unsigned NOT NULL,
                tip varchar(4) NOT NULL DEFAULT '',
                datum date NOT NULL DEFAULT '0000-00-00',
                destinacija varchar(50) NOT NULL DEFAULT '',
                smer varchar(50) NOT NULL DEFAULT '',
                ocena varchar(30) DEFAULT NULL,
                cas varchar(30) DEFAULT NULL,
                vrsta varchar(4) NOT NULL DEFAULT '',
                visina_smer varchar(15) NOT NULL DEFAULT '',
                visina_izstop varchar(15) NOT NULL DEFAULT '',
                pon_vrsta varchar(4) DEFAULT NULL,
                pon_nacin varchar(4) DEFAULT NULL,
                stil varchar(4) DEFAULT NULL,
                mesto varchar(4) DEFAULT NULL,
                partner varchar(50) DEFAULT NULL,
                opomba varchar(5) DEFAULT NULL,
                deleted int(1) unsigned DEFAULT NULL,
                PRIMARY KEY  (id)
            )";

        dbDelta($sql);
        
        if ($wpdb->last_error)
        {
            die($wpdb->last_error);
        }
    }

    public function deactivate()
    {
        return;
        
        /*
        global $wpdb;

        $show_errors = $wpdb->show_errors;
        $wpdb->show_errors = false;
        $denied = 'command denied to user';

        $wpdb->query("DROP TABLE " . $this->table_vzponi);
        
        if ($wpdb->last_error)
        {
            if (strpos($wpdb->last_error, $denied) === false)
            {
                die($wpdb->last_error);
            }
        }

        $wpdb->show_errors = $show_errors;
         *
         */
    }

    public function admin_menu()
    {
        add_menu_page('AO Kranj', 'AO Kranj', 'read', self::ID . '/app.php');
    }

    public function admin_init()
    {
    }

    public function admin_enqueue_scripts()
    {
        global $hook_suffix;
        
        switch ($hook_suffix)
        {
            case 'aokranj/app.php':
                // ext
                wp_enqueue_style('aokranj-bootstrap', AOKRANJ_PLUGIN_URL . 'bootstrap.css', array(), AOKRANJ_VERSION);
                wp_enqueue_script('aokranj-ext', AOKRANJ_PLUGIN_URL . 'ext/ext-dev.js', array(), AOKRANJ_VERSION);
                wp_enqueue_script('aokranj-bootstrap', AOKRANJ_PLUGIN_URL . 'bootstrap.js', array(), AOKRANJ_VERSION);
                wp_enqueue_script('aokranj-app', AOKRANJ_PLUGIN_URL . 'app.js', array(), AOKRANJ_VERSION);

                // app
                //wp_enqueue_style('aokranj-app', AOKRANJ_PLUGIN_URL . 'resources/app.css', array(), AOKRANJ_VERSION);
                //wp_enqueue_script('aokranj-app', AOKRANJ_PLUGIN_URL . 'resources/app.js', array(), AOKRANJ_VERSION);
                break;
        }
    }
    
    private function getRequestSort()
    {
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
        $directions = array('ASC', 'DESC');
        
        $property = 'datum';
        $direction = 'DESC';
        
        $s = filter_input(INPUT_GET, 'sort');
        $s = json_decode($s, true);
        if (is_array($s))
        {
            $s = $s[0];
            if (isset($s['property']) && in_array($s['property'], $properties))
            {
                $property = $s['property'];
            }
            if (isset($s['direction']) && in_array($s['direction'], $directions))
            {
                $direction = strtoupper($s['direction']);
            }
        }
        
        return array(
            'property' => $property,
            'direction' => $direction,
        );
    }
    
    private function getRequestPage()
    {
        $page = filter_input(INPUT_GET, 'page', FILTER_SANITIZE_NUMBER_INT);
        return (!empty($page)) ? $page : 1;
    }
    
    private function getRequestStart()
    {
        $start = filter_input(INPUT_GET, 'start', FILTER_SANITIZE_NUMBER_INT);
        return (!empty($start)) ? $start : 1;
    }
    
    private function getRequestLimit()
    {
        $limit = filter_input(INPUT_GET, 'limit', FILTER_SANITIZE_NUMBER_INT);
        return (!empty($limit)) ? $limit : 1;
    }
    
    /**
     * API functions
     */
    
    public function vzponi()
    {
        global $wpdb;
        
        $page = $this->getRequestPage();
        $start = $this->getRequestStart();
        $limit = $this->getRequestLimit();
        $sort = $this->getRequestSort();
        
        $vzponi = $wpdb->get_results(sprintf('
            SELECT *
            FROM %s
            WHERE user_id = %d
            ORDER BY %s %s
            LIMIT %d, %d',
            $this->table_vzponi,
            get_current_user_id(),
            $sort['property'],
            $sort['direction'],
            $start,
            $limit
        ));
        
        $total = $wpdb->get_var(sprintf('
            SELECT COUNT(id)
            FROM %s
            WHERE user_id = %d',
            $this->table_vzponi,
            get_current_user_id()
        ));
        
        $response = array(
            'success' => true,
            'data'    => $vzponi,
            'total'   => $total,
        );

        echo json_encode($response);
        
        die;
    }
    
    public function dodaj_vzpon()
    {
        $nonce = filter_input(INPUT_POST, 'nonce');
        wp_verify_nonce($nonce, 'aokranj-app');
        
        global $wpdb;
        
        // get values from $_POST
        $vzpon = array(
            'user_id' => get_current_user_id(),
            'tip' => filter_input(INPUT_POST, 'tip'),
            'destinacija' => filter_input(INPUT_POST, 'destinacija'),
            'smer' => filter_input(INPUT_POST, 'smer'),
            'datum' => filter_input(INPUT_POST, 'datum'),
            'ocena' => filter_input(INPUT_POST, 'ocena'),
            'cas' => filter_input(INPUT_POST, 'cas'),
            'vrsta' => filter_input(INPUT_POST, 'vrsta'),
            'visina_smer' => filter_input(INPUT_POST, 'visina_smer'),
            'visina_izstop' => filter_input(INPUT_POST, 'visina_izstop'),
            'pon_vrsta' => filter_input(INPUT_POST, 'pon_vrsta'),
            'pon_nacin' => filter_input(INPUT_POST, 'pon_nacin'),
            'stil' => filter_input(INPUT_POST, 'stil'),
            'mesto' => filter_input(INPUT_POST, 'mesto'),
            'partner' => filter_input(INPUT_POST, 'partner'),
            'opomba' => filter_input(INPUT_POST, 'opomba'),
        );
        
        // insert into db
        $wpdb->insert($this->table_vzponi, array_filter($vzpon));

        // read from db
        $vzpon = $wpdb->get_row("SELECT * FROM " . $this->table_vzponi . " WHERE id = " . $wpdb->insert_id);
        
        // build response
        $response = array(
            'success' => true,
            'msg'     => 'Vzpon je bil uspešno dodan.',
            'data'    => $vzpon
        );

        // encode it
        echo json_encode($response);

        // must die in admin-ajax.php call
        die;
    }

}
