# How to deploy to production

Two methods:
- Trigger an automated deployment (by pushing a correct git tag to the upstream repository)
- Manual procedure



## Automated deployment

General information:
- Deployments are implemented with git tags
- The latest prod-* tag is the one currently deployed to production

Prerequisites:
- The repo used for triggering a deployment must have a `git@github.com:aokranj/website-aokranj.com` remote configured


** Step #1** - Trigger the automated deployment:
```
./sbin/deploy-prod -y
```

** Step #2** - Follow the automated deployment:

Here: https://github.com/aokranj/website-aokranj.com/actions/workflows/deploy-to-prod.yml

** Step #3** - If needed, manually handle the (re)configuration tasks:
```
# Follow steps #1, #2, #4 and #5 below.
```



## Manual deployment

Prerequisites:
- You need to be added to the system group `ao-prod` to be able to manually deploy to production.

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
