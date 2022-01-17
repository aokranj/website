# Config map structure

Table of contents:
* [What is a "config map"?](#what-is-a-config-map)
* [Overall config map file structure](#overall-config-map-file-structure)
* [`metadata` specification](#metadata-specification)
* [`data` specification](#data-specification)
* [`string` type specification](#string-int-bool-type-specification)
* [`array` type specification](#array-type-specification)
* [Nested data structures](#nested-data-structures)



## What is a "config map"?

A config map is a data structure containing information about how to manipulate individual settings in the `wp_options` table.
A config map _container_ is a data structure holding config map data as well as its metadata.
A config map _file_ is a PHP file that contains (and returns!) a config map _container_.

Here is an example config map file:
```php
<?php

// The file MUST return an array
return [

  // Config map meta information
  'metadata' => [
    'version' => X,
    // ...
  ],

  // `wp_options` handling definitions
  'data' => [
    'siteurl'     => '<HERE-BE-YOUR-URL>',
    'admin_email' => '<HERE-BE-YOUR-EMAIL>',
    'active_plugins' => [
      'type' => 'serialized-array',
      'value' => [
        '1' => 'wp-cli-configmaps/wp-cli-configmaps.php',
        '2' => 'second-plugin/second-plugin.php',
      ],
    ],
    // ...
  ],
];
```



## Overall config map file structure

The config map file must adhere to the following:
- The file MUST return an array,
- The array must contain two sections:
-- `metadata` and
-- `data`.
- The `metadata` section MUST contain the `version` field.

Example:
```php
<?php

return [
    'metadata' => [
        'version' => X,
    ],
    'data' => [
        // ...
    ],
];
```



## `metadata` specification

The following metadata fields are supported:

| Metadata field | Value type         | Supported value(s) | Description |
| -------------- | ------------------ | ------------------ | ----------- |
| `version`      | non-negative int   | `1` (TODO)         | Version of a config map file/structure |



## `data` specification

The `data` structure is an array of "option definitions". Each option definition consists of:
- _A key_ that uniquely identifies the defined option
- _A specification_ of what to do with given option.

Here is the simplest config map with a single option definition:
```php
return [
    'metadata' => ...,
    'data' => [
        'siteurl => 'https://www.my-wordpress-site.example/',
    ],
];
```
Here, the _key_ of the option definition is `siteurl`, and `https://www.my-wordpress-site.example/` is the whole _specification_.
The meaning of this particular entry is simple - we want our `siteurl` option to have the value of `https://www.my-wordpress-site.example/`.

Internally, such^ option definition is fully expanded into the following structure:
```php
$configMap = [
    'siteurl' => [
        'type'         => 'string',
        'action-apply' => 'copy-as-is',
        'action-dump'  => 'copy-as-is',
        'value'        => 'https://www.my-wordpress-site.example/',
    ],
];
```
Lacking explicit definitions (for the sake of keeping the config map files more readable), some internal fields can be implicitly defined.
In this particular case, if the specification is of type `string`, it is implied that the intention is to transfer the given string value as-is.



## `string`, `int`, `bool` type specification

An option definition of type `string` supports the following fields:

| Spec field     | Context         | Supported value(s) | Default value | Description |
| -------------- | --------------- | ------------------ | ------------- | ----------- |
| `type`         | `dump`, `apply` | `string`, `int`, `bool` | (implicit)   | Type definition |
| `action`       | `dump`, `apply` | `copy-as-is`, `ignore`  | `copy-as-is` | How to transfer the option value in both directions |
| `action-apply` | `apply`         | `copy-as-is`, `ignore`, `delete` | `copy-as-is` | How to transfer the option value from a config map into the `wp_options` table |
| `action-dump`  | `dump`          | `copy-as-is`, `ignore` | `copy-as-is` | How to transfer the option value from `wp_options` table into a config map |



## `array` type specification

Besides strings, the `wp_options` table can also contain [serialized](https://www.php.net/manual/en/function.serialize.php) arrays and [JSON-encoded](https://www.php.net/manual/en/function.json-encode.php) data.

Here is an example option definition describing an array:
```php
$configMap = [
    'active_plugins' => [
        'type' => 'array',
        'encoding' => 'serialize',
        'value' => [
            '1' => 'wp-cli-configmaps/wp-cli-configmaps.php',
            '2' => 'second-plugin/second-plugin.php',
        ],
    ],
];
```

Internally, such^ option definition is fully expanded into the following structure:
```php
$configMap = [
    'active_plugins' => [
        'type'                   => 'array',
        'encoding'               => 'serialize',
        'action-apply'           => 'walk',
        'action-dump'            => 'walk',
        'undef-key-action-apply' => 'ignore',
        'undef-key-action-dump'  => 'add',
        'value'                  => [
            '1' => [
                'type'         => 'string',
                'action-apply' => 'copy-as-is',
                'action-dump'  => 'copy-as-is',
                'value'        => 'wp-cli-configmaps/wp-cli-configmaps.php',
            ],
            '2' => [
                'type'         => 'string',
                'action-apply' => 'copy-as-is',
                'action-dump'  => 'copy-as-is',
                'value'        => 'second-plugin/second-plugin.php',
            ],
        ],
    ],
];
```

An option definition of type `array` supports the following fields:

| Spec field     | Context         | Supported value(s)   | Default value | Description |
| -------------- | --------------- | -------------------- | ------------- | ----------- |
| `type`         | `dump`, `apply` | `array`              | `array`       | Type definition |
| `encoding`     | `dump`, `apply` | `serialize`, `json`, `none` | (undefined) | How data is encoded in the `wp_options` table |
| `action`       | `dump`, `apply` | `walk`, `copy-as-is`, `ignore` | `walk` | How to transfer the option value in both directions |
| `action-apply` | `apply`         | `walk`, `copy-as-is`, `ignore`, `delete` | `walk` | How to transfer the option value from a config map into the `wp_options` table |
| `action-dump`  | `dump`          | `walk`, `copy-as-is`, `ignore` | `walk` | How to transfer the option value from `wp_options` table into a config map |
| `undef-key-action-apply` | `apply` | `ignore`, `delete` | `ignore` | When applying config maps, how to treat keys found in the `wp_options` table that are not defined by any config map |
| `undef-key-action-dump`  | `dump`  | `ignore`, `add` | `add` for the first defined config map, `ignore` for all others | When updating config maps, how to treat keys found in the `wp_options` table that are not (yet) defined by any config map |
| `value`        | `dump`, `apply` | array | (undefined) | An array of option definitions |



## Nested data structures

The data type `array` of `serialize` or `json` encoding can have nested children.

An example config map with a nested data structure:
```php
$configMap = [
    'wp_user_roles' => [
        'type'     => 'array',
        'encoding' => 'serialize',
        'value' => [
            'contributor' => [
                'type' => 'array',
                'action' => 'traverse',
                'value' => [
                    'name' => [
                        'type' => 'string',
                        'action' => 'copy-as-is',
                        'value' => 'Contributor',
                    ],
                    'capabilities' => [
                        'type' => 'array',
                        'action' => 'traverse',
                        'value' => [
                            'edit_posts' => true,
                            'read' => true,
                            'level_1' => true,
                            'level_0' => true,
                            'delete_posts' => true,
                        ],
                    ],
                ],
            ],
            'subscriber' => [
                'type' => 'array',
                'action' => 'traverse',
                'value' => [
                    'name' => [
                        'type' => 'string',
                        'action' => 'copy-as-is',
                        'value' => 'Subscriber',
                    ],
                    'capabilities' => [
                        'type' => 'array',
                        'action' => 'traverse',
                        'value' => [
                            'read' => true,
                            'level_0' => true,
                        ],
                    ],
                ],
            ],
        ],
    ],
];
```
