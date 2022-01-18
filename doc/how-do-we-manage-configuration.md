# How do we manage WordPress configuration

The crux here is:
- How to make sure the configuration is always what we want(ed) it to be?
- How do we replicate this configuration between all environments?

The answer is [ConfigMaps](https://github.com/wp-cli-configmaps/wp-cli-configmaps).



## Where is our WordPress configuration stored?

Working configuration (the one that is actually used by WordPress itself) is (regretably) stored in the database, in the `wp_options` table.

But the source of truth for our configuration is in the config map files, located in the [conf/maps](../conf/maps) directory.



## Why are there multiple "config maps"?

Well:
- `conf/maps/common.php` file contains configuration options common to all environments
- `conf/maps/stg.php` contains options specific to https://stg.aokranj.com deployment
- `conf/maps/prod.php` contains options specific to https://www.aokranj.com deployment



## So how do I work with `configmaps`?

Simple.

To verify that database content matches config maps' definitions:
```
./wp configmaps verify
```

To export the working configuration into config maps (to "update" the config maps):
```
./wp configmaps update
```
Some manual tweaks are usually necessary before config maps are ready to be committed into a git repositoy.

To apply the configuration defined in config maps to the database:
```
./wp configmaps apply --commit
```

For the basics, that's it.
