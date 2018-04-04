# AO Kranj Wordpress




## Local development

For local development, the following software is required:
- docker-ce: https://docs.docker.com/install/
- docker-compose: https://docs.docker.com/compose/install/ (already included in docker-ce on OS X)

After you have installed the requirements above, this is how you run a local
instance of this blog:

    git clone git@git.teon.si:aokranj/website aokranj-website
    cd aokranj-website
    sudo docker-compose up

The first time it will take a few minutes because the web server image needs to
get built and the seed data needs to get imported into the database.
Once that is done, starting and stopping the container set is almost
instant.

Provided all went well, the local instance is waiting for you at:

| APP        | http://docker.dev.aokranj.com:8000/ | (user: ?, password: ?) |
| phpMyAdmin | http://docker.dev.aokranj.com:8001/ | (user: root, password: root) |

To jump into the web-serving container (to use "wp" cli tool, for example) use
the following command(s):

    sudo docker exec -ti website_web_1 /bin/bash
    cd /var/www/docker.dev.aokranj.com



## Automated deployments

Staging ~and production~ environments receive the code deployment automatically
whenever origin git repo receives the code into appropriate branch.

Whenever you push your code to the following branches, the deployment is
triggered automatically:

| branch | Deployment target URI |
| master | https://stg.aokranj.com/ |
| prod   | https://www.aokranj.com/ - TODO |

Deployment can take up to a few minutes, but usually it is completed in under a minute.
