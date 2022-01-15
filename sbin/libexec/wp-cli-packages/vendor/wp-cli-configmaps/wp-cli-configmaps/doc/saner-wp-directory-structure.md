# Saner WordPress directory structure

WordPress' default file structure (everything in a public directory) is pretty annoying.
We like to keep everything in git (sans environment secrets), and keep certain non-public
things outside of a public directory.
Here is a description of our WordPress directory structure in our git repository.



## Directory structure

Here:
```
./
./.git
./public                     # Here is the original WordPress code, and vhost root actually points to this location
./public/index.php
./public/wp-config.php       # This file is actually committed to the git repository, see it's content below

./conf
./conf/wp-config-local.php   # The actual local configuration (containing instance URLs, DB access credentials and salts, and WP_ENV definition)

./conf/maps                  # Location of our config maps
./conf/maps/common.php
./conf/maps/dev.php
./conf/maps/stg.php
./conf/maps/prod.php
./conf/maps/local.php
```



## Our `public/wp-config.php` file (example)

```php
<?php

/** Absolute path to the WordPress directory. */
if (!defined('ABSPATH')) {
    define( 'ABSPATH', __DIR__ . '/' );
}

/** Include the actual site configuration that is stored outside public directory */
require_once ABSPATH . '/../conf/wp-config-local.php';

/** Define potentially missing configuration defaults */
if (!defined('DB_HOST')) define('DB_HOST', 'localhost');
if (!isset($table_prefix)) $table_prefix = 'wp_';

/** Define a config map set */
$configMaps = [
    'common' => ABSPATH . '../conf/maps/common.php',
    WP_ENV   => ABSPATH . '../conf/maps/' . WP_ENV . '.php',
];
$localConfigMapPath = ABSPATH . '../conf/maps/local.php';
if (file_exists($localConfigMapPath)) {
    $configMaps['local'] = $localConfigMapPath;
}
define('WP_CLI_CONFIGMAPS', $configMaps);
unset($configMaps);

/** Define WP debug for dev and stg environments */
if ((WP_ENV == 'dev') || (WP_ENV == 'stg')) {
    define('WP_DEBUG', true);
} else {
    define('WP_DEBUG', false);
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
```



## Our `conf/wp-config-local.php` file

```php
<?php

/** Site environment & URL settings */
define('WP_ENV',     '');   // 'dev', 'stg' or 'prod'
define('WP_HOME',    'https://...');
define('WP_SITEURL', 'https://...');
//$_SERVER['HTTPS'] = 'on';   // Uncomment if behind TLS-terminating proxy (i.e. k8s ingress)

/** MySQL settings */
define( 'DB_HOST', '...' );
define( 'DB_NAME', '...' );
define( 'DB_USER', '...' );
define( 'DB_PASSWORD', '...' );
//define( 'DB_CHARSET', '...' );

/** Authentication unique keys and salts. */
define( 'AUTH_KEY',         '...' );
define( 'SECURE_AUTH_KEY',  '...' );
define( 'LOGGED_IN_KEY',    '...' );
define( 'NONCE_KEY',        '...' );
define( 'AUTH_SALT',        '...' );
define( 'SECURE_AUTH_SALT', '...' );
define( 'LOGGED_IN_SALT',   '...' );
define( 'NONCE_SALT',       '...' );
```



## Our `conf/maps/common.php` file

```php
<?php

return [
  'metadata' => [
    'version' => 1,
  ],
  'data' => [
    'active_plugins' => [
      'action' => 'walk',
      'encoding' => 'serialize',
      'type' => 'array',
      'value' => [
        1 => 'wp-cli-configmaps/wp-cli-configmaps.php',
      ],
    ],
    'admin_email' => '',
    'admin_email_lifespan' => '2000000000',
    'auto_update_core_dev' => 'enabled',
    'auto_update_core_major' => 'enabled',
    'auto_update_core_minor' => 'enabled',
    // ... and all other common settings
  ],
];
```



## Our `conf/maps/stg|prod.php` file

```php
<?php

return [
  'metadata' => [
    'version' => 1,
  ],
  'data' => [
    'admin_email' => '<EMAIL@DOMAIN.COM>',
    'home' => 'https://stg.instance.url.here',
    'siteurl' => 'https://stg.instance.url.here',
  ],
];
```



## Our `conf/maps/local.php` file (not committed)

```php
<?php

return [
  'metadata' => [
    'version' => 1,
  ],
  'data' => [
    'admin_email' => '<EMAIL@DOMAIN.COM>',
    'home' => 'https://my.dev.instance.url.here',
    'siteurl' => 'https://my.dev.instance.url.here',
  ],
];
```



## Author

Created by [Bostjan Skufca Jese](https://github.com/bostjan).
