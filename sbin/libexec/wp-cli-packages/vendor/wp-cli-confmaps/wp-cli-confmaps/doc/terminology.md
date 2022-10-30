# Terminology



## To `apply`

To apply defined conf map(s) to WordPress (more precisely, to `wp_options` database table).

Relevant `wp` commands:
```
wp confmaps apply
wp confmaps apply <map-id>
```


## To `update`

To update (either a selected one, or all defined) conf maps with values currently present at the corresponding locations in the `wp_options` table.

Mainly useful after installing new plugins or updating WordPress and/or plugins,
to find/fetch the new/updated configuration settings and have them be applied to all your WP environments.

Relevant `wp` commands:
```
wp confmaps update
wp confmaps update <map-id>
```



## Conf map

An array of WordPress configuration options' specifications (dump/apply definitions as well as values).

See [confmap-structure](confmap-structure.md) for more information.

| Variations | Explanation |
| ---------- | ----------- |
| Expanded conf map | A fully expanded conf map, with all option specification fields defined. |
| Minimal conf map  | A minimalized version of a conf map, with all fields that can be implicitly determined removed. Used for storing conf map in files. |



## Conf map container

An array containing both:
- Conf map metadata, and
- Conf map itself.

See [confmap-structure](confmap-structure.md) for more information.



## Conf map file

A PHP file containing a conf map _container_.

See [confmap-structure](confmap-structure.md) for more information.



## Key / Option key

A key that uniquely identifies an option, either at the top level (option name in a `wp_options` table) or in a nested array in one of the `wp_options` rows.



## Option specification

A specification of what to do with a particular option value.
This can be as simple as a regular string (to be copied as-is), or a complex array handling definition.
Can contain nested option specifications.

See [confmap-structure](confmap-structure.md) for more information.
