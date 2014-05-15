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
 * Dump anything
 */
function dump($data, $simple = true)
{
    echo '<pre style="font-family:\'Lucida Grande\',\'Lucida Sans Unicode\',Trebuchet;font-size:11px;border:1px solid #606060;color:#606060;background:#fafafa;margin:10px;padding:8px;">';
    $simple ? print_r($data) : var_dump($data);
    echo '</pre>';
}

/**
 * Global plugin instance
 */
$GLOBALS['AOKranj'] = new AOKranj();

/**
 * Plugin constants
 */
define('AOKRANJ_VERSION', '1.0');
define('AOKRANJ_PLUGIN_URL', plugin_dir_url(__FILE__));
define('AOKRANJ_PLUGIN_DIR', plugin_dir_path(__FILE__));

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

    protected $prefix = 'ao_';
    protected $table_vzponi = 'ao_vzponi';

    public function __construct()
    {
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
}
