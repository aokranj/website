<?php
/*
 * ConfMaps configuration management for WordPress WP-CLI - Tame your wp_options using WP-CLI and git
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

if (defined('WP_CLI')) {
    WP_CLI::add_command('confmaps', '\\WP\\CLI\\ConfMaps\\Commands');
}
