# Database transfers - Runbook - AO Kranj

A short guide how to migrate one environement's database into another environment (i.e. prod to stg).

Prerequisites:
- Access to target environment
- Access to source environment from the target environment
- Source and target environment must have the same WP tooling deployed



## Steps (prod|stg >> local docker dev)

Step #1 - Go to your dev environment:
```
cd website-aokranj.com
```

Step #2 - Dump+import the database in one go:
```
ssh www.aokranj.com /data/ao-prod/www.aokranj.com/sbin/db-dump | ./sbin/wp db import -   # From PROD
ssh stg.aokranj.com /data/ao-stg/stg.aokranj.com/sbin/db-dump  | ./sbin/wp db import -   # From STG
```

Step #3 - Fix the URLs in the new database copy:
```
./sbin/wp search-replace 'https://www.aokranj.com' 'https://docker.dev.aokranj.com'   # From PROD
./sbin/wp search-replace 'https://stg.aokranj.com' 'https://docker.dev.aokranj.com'   # From STG
```



## Steps (prod|stg >> on-server dev)

Step #1 - Go to your dev environment:
```
ssh stg.aokranj.com
cd www/YOURHOST.dev.aokranj.com
```

Step #2 - Dump+import the database in one go:
```
/data/ao-prod/www.aokranj.com/sbin/db-dump | ./sbin/wp db import -   # From PROD
/data/ao-stg/stg.aokranj.com/sbin/db-dump  | ./sbin/wp db import -   # From STG
```

Step #3 - Fix the URLs in the new database copy:
```
./sbin/wp search-replace 'https://www.aokranj.com' 'https://YOURHOST.dev.aokranj.com'   # From PROD
./sbin/wp search-replace 'https://stg.aokranj.com' 'https://YOURHOST.dev.aokranj.com'   # From STG
```



## Steps (prod >> stg)

Step #1 - SSH into the stg environment with auth forwarding enabled (`-A`):
```
ssh stg.aokranj.com
cd /data/ao-stg/stg.aokranj.com
```

Step #2 - Dump+import the database in one go (from PROD):
```
/data/ao-prod/www.aokranj.com/sbin/db-dump | ./sbin/wp db import -
```

Step #3 - Fix the URLs in the new database copy:
```
./sbin/wp search-replace 'https://www.aokranj.com' 'https://stg.aokranj.com'
```

Step #4 - Run the DB upgrade script (prod may have older WP version deployed, with order DB schema):
```
./sbin/deploy-here
```
