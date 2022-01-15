# Database transfers

A short guide how to migrate one environement's database into another environment (i.e. prod to stg).

Prerequisites:
- Access to target environment
- Access to source environment from the target environment
- If target environment is accessible via SSH, authentication forwarding must be enabled
- Source and target environment must have the same WP code deployed



## Steps (stg to docker dev)

Step #1 - Go to your dev environment:
```
cd website-aokranj.com
```

Step #2 - dump+import the database in one go:
```
ssh ao-stg@stg.aokranj.com ./www/stg.aokranj.com/sbin/db-dump | ./sbin/wp db import -
```

Step #3 - fix the URLs in the new database copy
```
./sbin/wp search-replace 'https://stg.aokranj.com' 'https://docker.dev.aokranj.com'
```



## Steps (prod to stg)

Step #1 - SSH into the stg environment with auth forwarding enabled (`-A`):
```
ssh ao-stg@stg.aokranj.com -A
cd www/stg.aokranj.com
```

Step #2 - dump+import the database in one go:
```
ssh ao-prod@www.aokranj.com ./www/www.aokranj.com/sbin/db-dump | ./sbin/wp db import -
```

Step #3 - fix the URLs in the new database copy
```
./sbin/wp search-replace 'https://www.aokranj.com' 'https://stg.aokranj.com'
```
