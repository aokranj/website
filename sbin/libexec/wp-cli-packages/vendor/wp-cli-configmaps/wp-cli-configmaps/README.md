# ConfigMaps for WordPress WP-CLI - Configuration management for your wp_options table

This is a [CLI](https://wp-cli.org/)-based tool for managing
[WordPress](https://wordpress.org/) settings defined in the
[wp_options](https://codex.wordpress.org/Database_Description#Table:_wp_options)
database table.

TL;DR this:
- This WP-CLI package manages WP options in your `wp_options` table
- Options' values are defined in PHP files
- Per-environment definitions are supported
- Definitions from multiple files files can be merged before applying the resulting configuration to the `wp_options` table
- You can dump database values back into the definition files ("config maps")

In short, if your answer to the question "Do I want to track my WordPress configuration that is stored in the `wp_options` table by storing it in a git repository?" is "Yes, definitely!", then this tool is what you're looking for.



## Installation

Prerequisites:
- [WordPress](https://wordpress.org/)
- [WP-CLI](https://wp-cli.org/)
- Shell access to your WP instance(s)

Install the `wp-cli-configmaps` package:
```
wp package install wp-cli-configmaps/wp-cli-configmaps
```



## Configuration

To start using this tool, at least one config map needs to be created.
The simplest way to get started is to generate a config map from your current `wp_options` content:
```
wp configmaps generate --from-db
```
This will dump out your new config map in a form of PHP code.

Store this generated config map in some file (i.e. `../conf/maps/common.php`).
You can do this file creation with the `generate` command too:
```
wp configmaps generate --from-db --output=../conf/maps/common.php
```

Now define your config map set:
```php
define('WP_CLI_CONFIGMAPS', [
    'common' => ABSPATH . '../conf/maps/common.php',
//  WP_ENV   => ABSPATH . '../conf/maps/'. WP_ENV .'.php',   // This one is for later, when you'll have a per-environment config map overlays
]);
```
You can add this^ to any suitable WordPress source file, however there is a dilemma:
- `wp-config.php` file, as intended by WordPress, should not be replicated between environments, but
- Changes to other WP source files will be overwritten by WordPress updates.

To help you with the decision where to put this code, see [a saner WordPress directory structure](doc/saner-wp-directory-structure.md) for a better way.



## Usage

To apply all options defined in your config map(s) to the database:
```
wp configmaps apply
```
This command transfers all defined option values (defined in one or multiple
[config map files](doc/terminology.md)) into the `wp_options` database table.
The transfer is performed according to the individual value specification (literal copy, merged, etc.).

Alternatively, if you've been tweaking your WordPress configuration in the admin section of a particular instance (i.e. your local development instance),
and now you want to transfer the new configuration to your other environments (i.e. staging and later production),
you can update your config maps with your current database values:
```
wp configmaps update
```
This will update all defined config maps in-place.
Now `git commit -av`, `git push`, `git pull` and `wp configmaps apply` are all that you need to reliably transfer this new configuration to all the other environments.



## License

```
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
```



## Author

Created by [Bostjan Skufca Jese](https://github.com/bostjan).
