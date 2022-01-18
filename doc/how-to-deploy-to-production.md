# How to deploy to production

General information:
- Deployments to staging are **NOT YET automated - TODO**
- ~~Whatever lands on a git branch `prod` is automatically deployed to production~~
- The repo clone used for deployment should already have `prod` branch checked out
- The repo clone used for deployment should be tracking the `git@github.com:aokranj/website-aokranj.com` upstream repository
- You need to be added to the system group `ao-prod` to be able to manually deploy to production



## Automated deployment

~~You can trigger the automated deployment by:~~
- ~~Pushing anything into the `prod` branch of the `github.com:aokranj/website-aokranj.com` repository~~
- ~~Merging a PR into the `prod` branch in the `github.com:aokranj/website-aokranj.com` repository~~
- ~~Running a deployment action manually on GitHub~~

~~You can follow the progress of automated deployments [here](https://github.com/aokranj/website-aokranj.com/actions).~~



## Manual deployment

**WARNING:** This method should only be used if automated deployment fails for some reason.
Make sure that automated deployments are not interfering with your manual work.

**Step #1** - `ssh` (with `-A`!):
```
ssh YOUR-USERNAME@www.aokranj.com -A
```

**Step #2** - `cd`:
```
cd /data/ao-prod/www.aokranj.com
```

**Step #3** - `git pull`:
```
git pull
```

**Step #4** - Run the deployment script:
```
./sbin/deploy-here
```

**Step #5** - Verify configuration:
```
./wp configmaps verify
```

All done.
