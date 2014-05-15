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
        add_menu_page('AO Kranj jQuery', 'AO Kranj jQuery', 'activate_plugins', self::ID . '/jquery.php');
        add_menu_page('AO Kranj ExtJS', 'AO Kranj', 'read', self::ID . '/ext.php');
    }

    public function admin_init()
    {
    }

    public function admin_enqueue_scripts()
    {
        global $hook_suffix;
        
        switch ($hook_suffix)
        {
            case 'aokranj/jquery.php':
                wp_enqueue_style('jquery-styles', AOKRANJ_PLUGIN_URL . 'resources/jquery/jquery-ui-1.10.4.custom.min.css', array(), AOKRANJ_VERSION);

                wp_enqueue_script('jquery-ui-core');
                wp_enqueue_script('jquery-ui-tabs');
                wp_enqueue_script('jquery-ui-datepicker');

                wp_enqueue_style('aokranj-jquery', AOKRANJ_PLUGIN_URL . 'resources/jquery/app.css', array(), AOKRANJ_VERSION);

                wp_enqueue_script('aokranj-jquery', AOKRANJ_PLUGIN_URL . 'resources/jquery/app.js', array(), AOKRANJ_VERSION);
                break;
            case 'aokranj/ext.php':
                // ext
                wp_enqueue_style('ext-styles', AOKRANJ_PLUGIN_URL . 'resources/ext/ext-theme-neptune-all-debug.css', array(), AOKRANJ_VERSION);
                wp_enqueue_script('ext', AOKRANJ_PLUGIN_URL . 'resources/ext/ext-all-debug.js', array(), AOKRANJ_VERSION);
                
                // model
                wp_enqueue_script('aokranj-model-vzpon', AOKRANJ_PLUGIN_URL . 'resources/app/model/Vzpon.js', array(), AOKRANJ_VERSION);
                
                // controller
                wp_enqueue_script('aokranj-controller-dodaj-vzpon', AOKRANJ_PLUGIN_URL . 'resources/app/controller/DodajVzpon.js', array(), AOKRANJ_VERSION);

                // view
                wp_enqueue_script('aokranj-view-tabs', AOKRANJ_PLUGIN_URL . 'resources/app/view/Tabs.js', array(), AOKRANJ_VERSION);
                wp_enqueue_script('aokranj-view-vzponi', AOKRANJ_PLUGIN_URL . 'resources/app/view/Vzponi.js', array(), AOKRANJ_VERSION);
                wp_enqueue_script('aokranj-view-dodaj-vzpon', AOKRANJ_PLUGIN_URL . 'resources/app/view/DodajVzpon.js', array(), AOKRANJ_VERSION);

                // app
                wp_enqueue_style('aokranj-ext', AOKRANJ_PLUGIN_URL . 'resources/app.css', array(), AOKRANJ_VERSION);
                wp_enqueue_script('aokranj-ext', AOKRANJ_PLUGIN_URL . 'resources/app.js', array(), AOKRANJ_VERSION);
                break;
        }
    }
    
    /**
     * API functions
     */
    
    public function vzponi()
    {
        global $wpdb;
        
        $vzponi = $wpdb->get_results("SELECT * FROM " . $this->table_vzponi . " WHERE user_id = " . get_current_user_id());

        echo json_encode($vzponi);
        
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
