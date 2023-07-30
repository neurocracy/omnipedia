# Deploying

Omnipedia has a fully automated deployment process on [DigitalOcean's App
Platform](https://docs.digitalocean.com/products/app-platform/) with a database
hosted on a [managed database
cluster](https://docs.digitalocean.com/products/databases/). See [our deploy
template](../.do/deploy.template.yaml) for an overview of how our app spec is
structured as it references the various build and run scripts we use. The App
Platform build process pulls in our many custom
[Composer](https://getcomposer.org/) packages and builds all front-end assets
they provide via [Yarn](https://yarnpkg.com/).
