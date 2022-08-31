# Upgrading WordPress

A short guide how to upgrade and deploy updated WP to all environments.

For this guide, let's assume the following configuration of remotes in your git repository (TL;DR branch `master` is tracking `aokranj/website-aokranj.com:master`, ditto for `prod`):
```
$ git remote -v
origin  git@github.com:bostjan/website-aokranj.com.git (fetch)
origin  git@github.com:bostjan/website-aokranj.com.git (push)
upstream        git@github.com:aokranj/website-aokranj.com (fetch)
upstream        git@github.com:aokranj/website-aokranj.com (push)

$ git br -vv -a
* master                  5e2a5a8 [upstream/master: ahead 2] conf: Change default email to janez.nastran[@]gmail.com + disable verification until sometime in 2030
  prod                    df8b648 [upstream/prod] Revert "Add plugin: the-events-calendar v5.7.0"
  remotes/origin/master   d50dd71 Update README.md
  remotes/upstream/HEAD   -> upstream/master
  remotes/upstream/master 10249de Merge pull request #12 from bostjan/docker-compose-dev-env
  remotes/upstream/prod   df8b648 Revert "Add plugin: the-events-calendar v5.7.0"
$
```



## Upgrade to the latest WordPress version (i.e. in your dev environment)

** Step #1** - Pull & checkout `master` branch (if you haven't already):
```
git checkout master
git pull
```
Here, make sure that you're pulling from the correct remote repository
(from `github.com:aokranj/website-aokranj.com` and not from `github.com:YOURUSERNAME/website-aokranj.com`).

**Step #2** - Upgrade the code (and store the output in the `commit-message-draft` file):
```
./sbin/upgrade-code | tee commit-message-draft
```

**Step #3** - Migrate the database:
```
./sbin/wp core update-db
```

**Step #4** - Verify the upgraded version:

Click around, make sure it works as expected.


**Step #5** - Commit:
```
git add public/
git commit -t commit-message-draft
rm commit-message-draft
```
Add the upgrade output to the commit message (it contains upgrade versioning information).



## Deploy to staging (stg.aokranj.com)

**Step #6** - Push `master` to upstream repository (deploy to STG)
```
git push   # or `git push upstream master`, if "upstream" is the name of the aokranj/website-aokranj.com remote
```

And done. Detailed information about deploying to staging is available [here](deploy-stg.md).



## Deploy to production

Production deployment guide is available [here](deploy-prod.md).



## Pull from upstream (when someone else has already committed the upgrade)

**Step #1** - Pull the new code:
```
git pull   # Or `git pull upstream master`
```

**Step #2 - Migrate the database + do other deployment-related tasks:
```
./sbin/deploy-here
```

Done.
