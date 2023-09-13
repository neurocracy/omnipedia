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

This will run [yarn.BUILD](https://yarn.build/) on all workspaces across the
project in development mode, which compiles most things but leaves out certain
assets (e.g. image processing) that usually only needs to be done in production.
To see the full production build, run

```bash
ddev yarn build:production
```

This will run the full production build, including minifying and optimizing
compiled CSS and other files.

Note that in both cases, yarn.BUILD will automagically only run build commands
on workspaces that it detects as having changed since the last build, or depend
on another that has changed. If you need to force a full rebuild, add the
`--ignore-cache` flag after either of the above commands like so:

```bash
ddev yarn build --ignore-cache
```

or

```bash
ddev yarn build:production --ignore-cache
```

It's at this point that you can visit the site in your browser at the URL DDEV
provides you to install Drupal into the database, although that's out of scope
for this guide for the time being. If you're feeling adventurous, after Drupal
is installed and running, you can install the majority of our Drupal
configuration (found in[`drupal_config`](../drupal_config)) by running

```bash
ddev drush -y config:import
```
