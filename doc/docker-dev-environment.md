# Set up local Docker-based development environment

Prerequisites:
- Access to https://github.com/aokranj/website-aokranj.com git repository
- SSH access to https://stg.aokranj.com (for database dump & `wp-content/uploads` content)
- Docker Desktop running on your workstation (or Linux workstation with Docker installed)



## Steps for initial setup


Step #1 - Clone the repository:
```
git clone git@github.com:aokranj/website-aokranj.com
```


Step #2 - Change directory into the cloned git repository:
```
cd website-aokranj.com
```


Step #3 - Configure your `conf/wp-config.php` file.
The `conf/wp-config.php.SAMPLE` file is already preconfigured for this use case,
so just get a new set of salts [here](https://api.wordpress.org/secret-key/1.1/salt/) and you're done:
```
cp conf/wp-config.php.SAMPLE conf/wp-config.php
edit conf/wp-config.php
```


Step #4 - Start the Docker-based dev environment:
```
./sbin/docker-compose-up
```


Step #5 - dump+import the staging database:
```
ssh ao-stg@stg.aokranj.com ./www/stg.aokranj.com/sbin/db-dump | ./sbin/wp-in-docker db import -
```


Step #6 - Fix the URLs in the new database copy
```
./sbin/wp-in-docker search-replace 'https://stg.aokranj.com' 'http://docker.dev.aokranj.com'
```


Step #7 - Fetch the `public/wp-content/uploads` content
```
rsync -av ao-stg@stg.aokranj.com:www/stg.aokranj.com/public/wp-content/uploads/ public/wp-content/uploads/
```


Step #8 - Fix the `public/wp-content/uploads` permissions:
```
chmod -R 777 public/wp-content/uploads
```


Step #9 (optional) - Create a local WP administrator:
```
./sbin/wp-in-docker user create YOUR-USERNAME-HERE YOUR-EMAIL-HERE --role=administrator --user_pass=YOUR-PASSWORD-HERE
```


That's it. Now your _own_ development environment is available at:
- http://docker.dev.aokranj.com/
- http://docker.dev.aokranj.com:81/ (phpMyAdmin)



## Steps for refreshing your dev environment

Just fetch the new code from git (`git pull`) and repeat steps #4 through #8 above.



## How to...


### How to run `wp`?

Either:
```
docker exec -ti pdkranj-webserver bash
./wp --allow-root
```
The `--allow-root` is needed because we're entering the container as root.

Or:
```
docker exec -ti pdkranj-webserver ./wp --allow-root
```

Or even shorter (does everything above for you):
```
./sbin/wp-in-docker
```



## Caveats

Caveat #1 - Email sent from this environment might be rejected like this (this response is from Google MX):
```
pdkranj-mail-out    |   276   ** bostjan@skufca.si R=dnslookup T=remote_smtp H=aspmx.l.google.com [64.233.184.26]
    X=TLS1.3:ECDHE_RSA_AES_256_GCM_SHA384:256 CV=no DN="CN=mx.google.com": SMTP error from remote mail server after pipelined end of data:
    550-5.7.1 [37.120.52.145] The IP you're using to send mail is not authorized to\n
    550-5.7.1 send email directly to our servers. Please use the SMTP relay at your\n
    550-5.7.1 service provider instead. Learn more at\n
    550 5.7.1  https://support.google.com/mail/?p=NotAuthorizedError c3si4159020wri.581 - gsmtp
```
