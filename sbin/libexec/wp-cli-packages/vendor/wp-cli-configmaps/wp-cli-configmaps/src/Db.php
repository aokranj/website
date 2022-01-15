<?php
/*
 * ConfigMaps for WordPress WP-CLI - Configuration management for your wp_options table
 *
 * Copyright (C) 2022 Bostjan Skufca Jese
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, see <https://www.gnu.org/licenses/gpl-2.0.html>.
 */

namespace WP\CLI\ConfigMaps;

if (!defined('WP_CLI')) {
    throw new Exception("Cannot run outside WP-CLI context");
}

class Db
{

    /**
     * Retrieve all options from the wp_options table
     *
     * (no further description)
     *
     * @return  array   An array of wp_options, directly from the database
     */
    public static function getAllOptions ()
    {
        global $wpdb;

        $rawOptions = [];

        $sqlQuery = "
            SELECT
                option_name,
                option_value
            FROM " . $wpdb->options . "
            WHERE
                1
                AND option_name NOT LIKE 'cron'
                AND option_name NOT LIKE '_transient%'
                AND option_name NOT LIKE '_site_transient%'
            ORDER BY
                option_name ASC";
        $results = $wpdb->get_results($sqlQuery);

        foreach ($results as $result) {
            $rawOptions[$result->option_name] = $result->option_value;
        }

        return $rawOptions;
    }

    /**
     * Insert an option value into the wp_options table
     *
     * (no further description)
     *
     * @param   string  $optionName     Name identifier of an option in wp_options table
     * @param   string  $value       Value of the option
     * @return  void
     */
    public static function insertOption ($optionName, $value)
    {
        global $wpdb;

        if ($optionName == 'cron') {
            throw new Exception("Managing `cron` option is not yet supported");
        }

        $wpdb->insert($wpdb->options, [
            'option_name'  => $optionName,
            'option_value' => $value,
            'autoload'     => 'yes',
        ]);
    }

    /**
     * Update an option value in the wp_options table
     *
     * (no further description)
     *
     * @param   string  $optionName     Name identifier of an option in wp_options table
     * @param   string  $newValue       New value of the option
     * @return  void
     */
    public static function updateOption ($optionName, $newValue)
    {
        // This one is trying to send email when i.e. admin_email is updated, not something we want.
        //update_option($optionName, $newValue);

        global $wpdb;

        if ($optionName == 'cron') {
            throw new Exception("Managing `cron` option is not yet supported");
        }

        $wpdb->update($wpdb->options, ['option_value' => $newValue], ['option_name' => $optionName]);
    }

    /**
     * Delete an option (row) from the wp_options table
     *
     * (no further description)
     *
     * @param   string  $optionName     Name identifier of an option in wp_options table
     * @return  void
     */
    public static function deleteOption ($optionName)
    {
        global $wpdb;

        if ($optionName == 'cron') {
            throw new Exception("Managing `cron` option is not yet supported");
        }

        $wpdb->delete($wpdb->options, ['option_name' => $optionName]);
    }
}
