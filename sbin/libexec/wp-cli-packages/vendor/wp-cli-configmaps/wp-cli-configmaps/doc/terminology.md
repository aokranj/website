# Terminology



## To `apply`

To apply defined config map(s) to WordPress (more precisely, to `wp_options` database table).

Relevant `wp` commands:
```
wp configmaps apply
wp configmaps apply <map-id>
```


## To `update`

To update (either a selected one, or all defined) config maps with values currently present at the corresponding locations in the `wp_options` table.

Mainly useful after installing new plugins or updating WordPress and/or plugins,
to find/fetch the new/updated configuration settings and have them be applied to all your WP environments.

Relevant `wp` commands:
```
wp configmaps update
wp configmaps update <map-id>
```



## Config map

An array of WordPress configuration options' specifications (dump/apply definitions as well as values).

See [configmap-structure](configmap-structure.md) for more information.

| Variations | Explanation |
| ---------- | ----------- |
| Expanded config map | A fully expanded config map, with all option specification fields defined. |
| Minimal config map  | A minimalized version of a config map, with all fields that can be implicitly determined removed. Used for storing config map in files. |



## Config map container

An array containing both:
- Config map metadata, and
- Config map itself.

See [configmap-structure](configmap-structure.md) for more information.



## Config map file

A PHP file containing a config map _container_.

See [configmap-structure](configmap-structure.md) for more information.



## Key / Option key

A key that uniquely identifies an option, either at the top level (option name in a `wp_options` table) or in a nested array in one of the `wp_options` rows.



## Option specification

A specification of what to do with a particular option value.
This can be as simple as a regular string (to be copied as-is), or a complex array handling definition.
Can contain nested option specifications.

See [configmap-structure](configmap-structure.md) for more information.
