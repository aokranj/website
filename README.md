# AO Kranj Wordpress



## Local development

For the initial setup, follow the [Docker-based local development environment setup](doc/docker-dev-environment.md) guide.

Here are a few additional guides:
- For keeping the content in sync with stg/prod, follow [this](database-transfers.md) guide
- [How to add a new WP plugin?](doc/howto-plugin-add.md)
- [How to remove a WP plugin?](doc/howto-plugin-remove.md)
- [How to manage WP configuration?](doc/howto-configuration-management.md)



## Deployments to staging and production

Code is deployed to STG and PROD automatically under the following conditions:
- Anything that lands in the `master` branch is immediately deployed to STG,
- When a new signed `prod-YYYYMMDD-HHMMSS` tag is created, that immediately triggers deployment to PROD.

| Ref                        | Deployment target URI    | Detailed+manual deployment guide                  |
| -------------------------- | ------------------------ | ------------------------------------------------- |
| Branch `master`            | https://stg.aokranj.com/ | [How to deploy to staging](doc/deploy-stg.md)     |
| Tag `prod-YYYYMMDD-HHMMSS` | https://www.aokranj.com/ | [How to deploy to production](doc/deploy-prod.md) |

You can observe the progress of deployments by following appropriate [GitHub Actions](https://github.com/aokranj/website-aokranj.com/actions).
