# AO Kranj Wordpress



## Local development

For the initial setup, follow the [Docker-based local development environment setup](doc/docker-dev-environment.md) guide.

Here are a few additional guides:
- For keeping the content in sync with stg/prod, follow [this](doc/database-transfers.md) guide
- [How to add a new WP plugin?](doc/howto-plugin-add.md)
- [How to remove a WP plugin?](doc/howto-plugin-remove.md)
- [How to manage WP configuration?](doc/howto-configuration-management.md)



## Runbooks

Procedures for common tasks that need to be done repeatedly are described in documents called **runbooks**.
Here are the main ones:
- How to [update WordPress](doc/runbook/update-wordpress.md)

The whole collection of runbooks is located in the [doc/runbook/](doc/runbook/) directory.



## Deployments to staging and production

Automatic code deployment to STG and PROD is triggered with the following actions:
- Anything that is pushed to `master` branch in [our main git repository](https://github.com/aokranj/website-aokranj.com) is immediately deployed to STG,
- When a new signed `prod-YYYYMMDD-HHMMSS` tag is pushed, that tag is immediately deployed to PROD.

| Ref                        | Deployment target URI    | Detailed+manual deployment guide                  |
| -------------------------- | ------------------------ | ------------------------------------------------- |
| Branch `master`            | https://stg.aokranj.com/ | [How to deploy to staging](doc/deploy-stg.md)     |
| Tag `prod-YYYYMMDD-HHMMSS` | https://www.aokranj.com/ | [How to deploy to production](doc/deploy-prod.md) |

You can observe the progress of deployments by following appropriate [GitHub Actions](https://github.com/aokranj/website-aokranj.com/actions).
