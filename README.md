# AO Kranj Wordpress



## Local development

For the initial setup, follow the [Docker-based local development environment setup](doc/docker-dev-environment.md) guide.

Here are a few additional guides:
- For keeping the content in sync with stg/prod, follow [this](database-transfers.md) guide
- [How to add a new plugin?](doc/how-to-add-a-new-plugin.md)
- [How to remove a plugin?](doc/how-to-remove-a-plugin.md)
- [How do we manage configuration?](doc/how-do-we-manage-configuration.md)



## Deployments to staging and production

Whenever you push your code to the following branches, the deployment is triggered automatically:

| branch | Deployment target URI           | Manual deployment guide |
| ------ | ------------------------------- | ----------------------- |
| master | https://stg.aokranj.com/        | [How to deploy to staging](doc/how-to-deploy-to-staging.md) |
| prod   | https://www.aokranj.com/ - TODO | [How to deploy to production](doc/how-to-deploy-to-production.md) |

You can observe the progress of deployments by following appropriate [GitHub Actions](https://github.com/aokranj/website-aokranj.com/actions).
