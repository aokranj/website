<?php

/**
 * Plugin Name: AO Kranj
 *
 * Description: AO Kranj Wordpress plugin.
 * Version: 1.0
 * Author: Bojan Hribernik
 * Author URI: http://aokranj.com/
 * @package aokranj
 */

/**
 * Load configuration
 */
require_once 'config.php';

/**
 * Global plugin instance
 */
$GLOBALS['AOKranj'] = new AOKranj();

/**
 * AOKranj
 *
 * @package aokranj
 * @link http://aokranj.com/
 * @author Bojan Hribernik <bojan.hribernik@gmail.com>
 */
class AOKranj
{
    const ID = 'aokranj';
    const NAME = 'AO Kranj';
    const VERSION = '1.0';
    
    const USER_STATUS_NORMAL  = 0;
    const USER_STATUS_WAITING = 2;

    protected $aodb = null;
    protected $prefix = 'ao_';
    
    protected $table_vzponi = 'ao_vzponi';

    public function __construct()
    {
        add_action('wp_authenticate', array(&$this, 'wp_authenticate'));
        
        if (is_admin())
        {
            require_once dirname(__FILE__) . '/admin.php';
            $admin = new AOKranj_Admin();

            if (is_multisite())
            {
                $admin_menu = 'network_admin_menu';
                $admin_notices = 'network_admin_notices';
            }
            else
            {
                $admin_menu = 'admin_menu';
                $admin_notices = 'admin_notices';
            }

            add_action($admin_menu, array(&$admin, 'admin_menu'));
            add_action('admin_init', array(&$admin, 'admin_init'));

            register_activation_hook(__FILE__, array(&$admin, 'activate'));
            register_deactivation_hook(__FILE__, array(&$admin, 'deactivate'));
        }
    }
    
    protected function aodb()
    {
        if (is_null($this->aodb))
        {
            $this->aodb = new wpdb(AO_DB_USER, AO_DB_PASSWORD, AO_DB_NAME, AO_DB_HOST);
        }
        return $this->aodb;
    }
    
    public function wp_authenticate()
    {        
        // user login
        $username = filter_input(INPUT_POST, 'log');
        $password = filter_input(INPUT_POST, 'pwd');
        
        // fetch wordpress user
        $wp_user = apply_filters('authenticate', null, $username, $password);
        
        if ($wp_user == null)
        {
            $wp_user = new WP_Error('authentication_failed', __('<strong>ERROR</strong>: Invalid username or incorrect password.'));
        }
        
        if (is_wp_error($wp_user) && !in_array($wp_user->get_error_code(), array('empty_username', 'empty_password')))
        {
            if ($username && $password)
            {
                $wp_user = $this->transfer_user_password($username, $password);
            }
            
            if (is_wp_error($wp_user))
            {
                do_action('wp_login_failed', $username);
            }
        }
        
        return $wp_user;
    }
    
    private function transfer_user_password($username, $password)
    {
        global $wpdb;
        $aodb = $this->aodb();
        
        $ao_field = strstr($username, '@') ? 'email' : 'userName';
        $wp_field = strstr($username, '@') ? 'user_email' : 'user_login';

        $ao_user = $aodb->get_row(sprintf(
            'SELECT * FROM member WHERE %s = \'%s\' AND userPass = \'%s\'',
            esc_sql($ao_field),
            esc_sql($username),
            esc_sql(md5($password))
        ));

        $wp_user = $wpdb->get_row(sprintf(
            'SELECT * FROM %s WHERE %s = \'%s\' AND user_status = %d',
            esc_sql($wpdb->users),
            esc_sql($wp_field),
            esc_sql($username),
            self::USER_STATUS_WAITING
        ));

        if (!$ao_user || !$wp_user)
        {
            return false;
        }
        
        $success = $wpdb->query(sprintf(
            'UPDATE %s SET user_pass = \'%s\', user_status = %d WHERE ID = %d',
            esc_sql($wpdb->users),
            esc_sql(wp_hash_password($password)),
            self::USER_STATUS_NORMAL,
            $wp_user->ID
        ));
        
        if ($success)
        {
            wp_cache_delete($wp_user->ID, 'users');
            
            $wp_user = apply_filters('authenticate', null, $wp_user->user_login, $password);
            
            if ($wp_user === null)
            {
                $wp_user = new WP_Error('authentication_failed', __('<strong>ERROR</strong>: Invalid username or incorrect password.'));
            }
        }
        else
        {
            $wp_user = new WP_Error('authentication_failed', __('<strong>ERROR</strong>: Invalid username or incorrect password.'));
        }
        
        return $wp_user;
    }
    
}
