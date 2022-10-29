# How do we manage WordPress configuration

The crux here is:
- How to make sure the configuration is always what we want(ed) it to be?
- How do we replicate this configuration between all environments?

The answer is [ConfMaps](https://github.com/wp-cli-confmaps/wp-cli-confmaps).



## Where is our WordPress configuration stored?

Working configuration (the one that is actually used by WordPress itself) is (regretably) stored in the database, in the `wp_options` table.

But the source of truth for our configuration is in the conf map files, located in the [conf/maps](../conf/maps) directory.



## Why are there multiple "conf maps"?

Well:
- `conf/maps/common.php` file contains configuration options common to all environments
- `conf/maps/stg.php` contains options specific to https://stg.aokranj.com deployment
- `conf/maps/prod.php` contains options specific to https://www.aokranj.com deployment



## So how do I work with `confmaps`?

Simple.

To verify that database content matches conf maps' definitions:
```
./wp confmaps verify
```

To export the working configuration into conf maps (to "update" the conf maps):
```
./wp confmaps update
```
Some manual tweaks are usually necessary before conf maps are ready to be committed into a git repositoy.

To apply the configuration defined in conf maps to the database:
```
./wp confmaps apply --commit
```

For the basics, that's it.
