<?php

/**
 * AO Kranj wordpress plugin settings
 */
define('AOKRANJ_DEBUG', false);
define('AOKRANJ_PLUGIN_VERSION', '1.0');
define('AOKRANJ_PLUGIN_URL', rtrim(plugin_dir_url(__FILE__), '/'));
define('AOKRANJ_PLUGIN_DIR', rtrim(plugin_dir_path(__FILE__), '/'));

/**
 * AO Kranj tables
 */
define('AOKRANJ_TABLE_VZPONI', 'ao_vzponi');

/**
 * AO Kranj old website settings
 */
define('AOKRANJ_OLD_DIR', '/home/ao/www/www.aokranj.com/public');
define('AOKRANJ_OLD_DB_NAME', 'aokranj_com');
define('AOKRANJ_OLD_DB_USER', 'bojan');
define('AOKRANJ_OLD_DB_PASSWORD', 'KladuPaKl1n');
define('AOKRANJ_OLD_DB_HOST', 'localhost');
define('AOKRANJ_OLD_DB_CHARSET', 'utf8');
define('AOKRANJ_OLD_DB_COLLATE', '');

/**
 * Temporary
 */
function dump($data, $simple = true)
{
    echo '<pre style="font-family:\'Lucida Grande\',\'Lucida Sans Unicode\',Trebuchet;font-size:11px;border:1px solid #606060;color:#606060;background:#fafafa;margin:10px;padding:8px;">';
    $simple ? print_r($data) : var_dump($data);
    echo '</pre>';
}
