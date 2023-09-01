# Local development

This documentation assumes some familiarty with Drupal and PHP development;
there are many good resources out there for working with this complex and
robust system, but these are largely out of scope of this document; a good
place to start is [the Drupal.org developer
documentation](https://www.drupal.org/docs/develop).

Omnipedia is developed on Linux and run locally via [DDEV](https://ddev.com/)
with our configuration found in
[`.ddev/config.yaml`](../.ddev/config.yaml). It can be started by running

```bash
ddev start
```

in the project root. Note that this will likely need to download various Docker
images the first time, some of which are several hundred megabytes.

Once DDEV has started all of the containers, you'll be able to build the
codebase by running

```bash
ddev composer install && ddev yarn install
```

Once Composer and Yarn have finished pulling in all dependencies, you can build
all of their front-end assets (CSS, images, icons, etc.) by running

```bash
ddev yarn build
```

It's at this point that you can visit the site in your browser at the URL DDEV
provides you to install Drupal into the database, although that's out of scope
for this guide for the time being. If you're feeling adventurous, after Drupal
is installed and running, you can install the majority of our Drupal
configuration (found in[`drupal_config`](../drupal_config)) by running

```bash
ddev drush -y config:import
```
