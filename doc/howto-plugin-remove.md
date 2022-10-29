# How to remove a plugin from our WordPress

Ok, so we've decided to not much about with our WordPress deployment:
- PHP files are not webserver-writable
- FTP access to stg/prod environments does not exist
- We track our WordPress code in a git repository

This means that classic method (point-n-click) of plugin installation in WordPress admin ain't gonna cut it here.



## Before you begin

- Have your own [Docker-based development environment](docker-dev-environment.md) set up
- Do NOT commit code to the `master` branch in order to test it out on staging - test it locally in your own development environment
- PRs (or `master` branch) should only receive code that is very likely to end up in production
- Bring your local dev environment's code (and database) into sync (with either staging or production)



## Remove the plugin locally

Use WP-CLI for this:
```
./wp plugin deactivate PLUGIN-SLUG
./wp plugin uninstall  PLUGIN-SLUG
```



## Export the WordPress' configuration

Like this:
```
./wp confmaps update
```

This will update defined conf maps in-place.
Use `git diff` to review the new configuration directives that this plugin has added.
You'll most likely need to tweak the updated config options, i.e.:
- Make sure they are located in the right conf map file (i.e. when they are environment-specific)
- Remove changes that are either irrelevant or wrong (i.e. domain names)

Once you've tweaked the conf maps, verify that applying them results in the same configuration as is currently stored in your database:
```
./wp confmaps verify
```
The output should only contain the following message:
```
Success: Database table wp_options is already consistent with the defined conf maps.
```



## Commit & create a pull request

Now, before committing anything, it's a good time to create a new git branch, which will be used for your new pull request (PR).

Then follow with a regular git-based workflow (new branch, commit, push to your fork on github, create a PR, have it reviewed and then merged).
