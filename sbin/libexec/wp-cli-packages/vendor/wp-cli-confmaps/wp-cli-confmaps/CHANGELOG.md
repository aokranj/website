# Changelog - ConfMaps configuration management for WordPress WP-CLI



## 2022-10-29 - Release 1.0.5

Project renamed to `wp-cli-confmaps`.



## 2022-01-17 - Release 1.0.4

Bugs fixed:
- Fix missing encoding for newly created options (defined in maps, completely absent from `wp_options`)



## 2022-01-16 - Release 1.0.3

Improvements:
- Change how the default value for `undef-key-action-dump` is defined (`add` for the first map, `ignore` for all subsequent maps)
- Ignore a few more irrelevant wp_options: `can_compress_scripts`, `finished_updating_coment_type` and `recently_edited`

Bugs fixed:
- When checking arrays for new keys during map updates, check for null value before attempting to parse it



## 2022-01-12 - Release 1.0.2

Bugs fixed:
- Add missing handling for `optionSpec['undef-key-action-apply'] == 'delete'` definition



## 2022-01-12 - Release 1.0.1

Bugs fixed:
- Add missing handling for `optionSpec['undef-key-action-dump'] == 'add'` definition
- Remove redundant `'action' => 'walk'` from minimized conf maps (it's the default action)



## 2022-01-11 - Release 1.0.0

Initial release.
