<?php

// phpcs:ignoreFile

declare(strict_types=1);

/**
 * @file
 * Customized and stripped down Drupal configuration file.
 *
 * @see default.settings.php
 *   Contains full documentation.
 */

/**
 * Database connection settings.
 *
 * This first attempts to use 'DRUPAL_DATABASE_*' environment variables to build
 * the primary/default connection if they're all available, falling back to
 * requiring a settings.database.php file in the site directory if not a DDEV
 * project.
 */
if (
  \getenv('DRUPAL_DATABASE_NAME')     !== false &&
  \getenv('DRUPAL_DATABASE_USERNAME') !== false &&
  \getenv('DRUPAL_DATABASE_PASSWORD') !== false &&
  \getenv('DRUPAL_DATABASE_HOSTNAME') !== false &&
  \getenv('DRUPAL_DATABASE_PORT')     !== false
) {

  $databases['default']['default'] = [
    'database'  => \getenv('DRUPAL_DATABASE_NAME'),
    'username'  => \getenv('DRUPAL_DATABASE_USERNAME'),
    'password'  => \getenv('DRUPAL_DATABASE_PASSWORD'),
    'host'      => \getenv('DRUPAL_DATABASE_HOSTNAME'),
    'port'      => \getenv('DRUPAL_DATABASE_PORT'),
    'driver'    => 'mysql',
    'prefix'    => '',
  ];

} else if (
  \getenv('IS_DDEV_PROJECT') === false &&
  \file_exists($app_root . '/' . $site_path . '/settings.database.php')
) {
  require $app_root . '/' . $site_path . '/settings.database.php';
}

/**
 * Set the MySQL transaction isolation level as recommended by Drupal.
 *
 * @see https://www.drupal.org/docs/system-requirements/setting-the-mysql-transaction-isolation-level
 *
 * @todo Is this still needed as of Drupal core 10?
 */
foreach ($databases as $databaseKey => &$database) {

  foreach ($databases[$databaseKey] as $targetKey => &$target) {

    if ($target['driver'] !== 'mysql') {
      continue;
    }

    $target = \array_merge_recursive($target, ['init_commands' => [
      'isolation_level' =>
        'SET SESSION TRANSACTION ISOLATION LEVEL READ COMMITTED',
    ]]);

  }

}

/**
 * Location of the site configuration files.
 *
 * The $settings['config_sync_directory'] specifies the location of file system
 * directory used for syncing configuration data. On install, the directory is
 * created. This is used for configuration imports.
 *
 * The default location for this directory is inside a randomly-named
 * directory in the public files path. The setting below allows you to set
 * its location.
 */
$settings['config_sync_directory'] = $app_root . '/../drupal_config/sync';

/**
 * Enable configuration splits from environment variable if found.
 *
 * This reads a comma-separated list from the 'DRUPAL_CONFIG_SPLITS' environment
 * variable and enables the ones that are listed.
 *
 * @see https://www.drupal.org/project/config_split/issues/3109103
 *   If you need a module to only be enabled in production but not in other
 *   environments (an example being the Paranoia module), you can create a
 *   configuration split that enables such modules and only enable that split in
 *   production. See the issue for more information.
 */
if (\getenv('DRUPAL_CONFIG_SPLITS') !== false) {

  $configSplitNames = \explode(',', \getenv('DRUPAL_CONFIG_SPLITS'));

  foreach ($configSplitNames as $i => $name) {

    if (\mb_strlen($name) === 0) {
      continue;
    }

    $config['config_split.config_split.' . $name]['status'] = true;

  }

}

/**
 * Salt for one-time login links, cancel links, form tokens, etc.
 */
if (\getenv('DRUPAL_HASH_SALT') !== false) {

  $settings['hash_salt'] = \getenv('DRUPAL_HASH_SALT');

}

/**
 * Deployment identifier.
 *
 * Drupal's dependency injection container will be automatically invalidated and
 * rebuilt when the Drupal core version changes. When updating contributed or
 * custom code that changes the container, changing this identifier will also
 * allow the container to be invalidated as soon as code is deployed.
 */
if (\getenv('DEPLOYMENT_IDENTIFIER') !== false) {
  $settings['deployment_identifier'] = \getenv('DEPLOYMENT_IDENTIFIER');
}

/**
 * Access control for update.php script.
 *
 * Under almost all circumstances, this should be left as false as updating
 * should be done via much more secure means - preferably via Drush and not
 * update.php. Note that web access to update.php is also blocked via .htaccess
 * rules prepended through the Drupal scaffolding process.
 */
$settings['update_free_access'] = false;

/**
 * Primary host name if set via the 'DRUPAL_PRIMARY_HOST' environment variable.
 */
if (\getenv('DRUPAL_PRIMARY_HOST') !== false) {
  $settings['primary_host'] = \getenv('DRUPAL_PRIMARY_HOST');
}

/**
 * Media oEmbed domain name via the 'DRUPAL_OEMBED_HOST' environment variable.
 *
 * This avoids having to hard code a domain name in the exported
 * configuration, instead setting it dynamically.
 *
 * Note that this is in the host format without the protocol, so we have to
 * prepend 'https://'.
 */
if (\getenv('DRUPAL_OEMBED_HOST') !== false) {
  $config['media.settings']['iframe_domain'] = 'https://' . \getenv(
    'DRUPAL_OEMBED_HOST'
  );
}

/**
 * Public file path.
 *
 * A local file system path where public files will be stored. This directory
 * must exist and be writable by Drupal. This directory must be relative to
 * the Drupal installation directory and be accessible over the web.
 *
 * Note that this still needs to be set when the S3 File System public/private
 * takeover is enabled to prevent warnings on the status report page.
 */
$settings['file_public_path'] = 'sites/default/files';

/**
 * Optimized assets path.
 *
 * A local file system path where optimized assets will be stored. This
 * directory must exist and be writable by Drupal. This directory must be
 * relative to the Drupal installation directory and be accessible over the
 * web.
 *
 * @see https://www.drupal.org/project/drupal/issues/3027639
 *   Requires this patch to use in Drupal core older than 10.1
 */
$settings['file_assets_path'] = 'assets';

/**
 * Private file path.
 *
 * A local file system path where private files will be stored. This directory
 * must be absolute, outside of the Drupal installation directory and not
 * accessible over the web.
 *
 * Note that this still needs to be set when the S3 File System public/private
 * takeover is enabled to prevent warnings on the status report page.
 *
 * @see https://www.drupal.org/documentation/modules/file
 */
$settings['file_private_path'] = \realpath(
  $app_root . '/../drupal_private_files'
);

/**
 * Enable S3 File System public/private takeover if environment variable exists.
 *
 * The public and private file paths should still be set above regardless so
 * Drupal won't complain. The S3 module will still override them when the below
 * are active.
 */
if (\getenv('S3FS_TAKEOVER') !== false) {
  $settings['s3fs.use_s3_for_public']   = true;
  $settings['s3fs.use_s3_for_private']  = true;
}

/**
 * Reverse Proxy Configuration.
 *
 * If the environment variable is defined, we enable the reverse proxy
 * functionality in Drupal core. This setting also instructs the Trusted
 * Reverse Proxy module to autofill the required IP addresses that Drupal core
 * requires in $settings['reverse_proxy_addresses'] for this to be enabled.
 *
 * This is necessary to enable secure session cookies on DigitalOcean App
 * Platform. Why is this? In the web service, $_SERVER['REQUEST_SCHEME'] is
 * 'http' and not 'https' in the app, very likely because the connections
 * within the app itself are not encrypted between services running inside the
 * app, but only get encrypted (and thus HTTPS) once they leave the app.
 * Drupal and Symfony will only send an HTTP (non-secure) session cookie and
 * not a secure session cookie, as they don't know the connection between the
 * client and Cloudflare or even between the DigitalOcean data centre and
 * Cloudflare are HTTPS.
 *
 * To have Symfony accept the "X-Forwarded-Proto" header which contains
 * 'https' from Cloudflare, we need to also provide the IP addresses for the
 * trusted reverse proxies. This is where the Trusted Reverse Proxy module
 * comes in to automate the process.
 *
 * @see https://www.drupal.org/project/trusted_reverse_proxy
 *
 * @see https://drupal.stackexchange.com/questions/269640/where-to-use-settrustedproxies-or-how-to-set-new-trusted-proxies
 *
 * @see https://github.com/symfony/symfony/blob/6.2/src/Symfony/Component/HttpFoundation/Request.php#L1078
 */
if (\getenv('DRUPAL_REVERSE_PROXY_ENABLED') !== false) {

  $settings['reverse_proxy'] = true;

  // These are the only headers that the Symfony Request object supports out of
  // the box that Cloudflare sends.
  //
  // @see https://developers.cloudflare.com/fundamentals/get-started/reference/http-request-headers/
  $settings['reverse_proxy_trusted_headers'] =
    \Symfony\Component\HttpFoundation\Request::HEADER_X_FORWARDED_FOR |
    \Symfony\Component\HttpFoundation\Request::HEADER_X_FORWARDED_PROTO;

}

/**
 * Drupal session cookie name prefix.
 *
 * This uses the '__Host-' prefix for security hardening by default. If the
 * 'DRUPAL_SESSION_COOKIE_PREFIX' environment variable is set, it will use that
 * instead.
 *
 * Note that '__Host-' will be rejected by browsers if on a subdomain, in which
 * case setting the provided environment variable to '__Secure-' is the next
 * best thing.
 *
 * @see https://www.drupal.org/project/drupal/issues/1361742
 *   Requires a patch from this merge request at time of writing.
 *
 * @see https://scotthelme.co.uk/tough-cookies/#cookieprefixes
 *   Excellent article on cookie security hardening.
 *
 * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Set-Cookie#attributes
 *   Description of '__Secure-' and '__Host-' semantics.
 */
if (\getenv('DRUPAL_SESSION_COOKIE_PREFIX') !== false) {
  $settings['session_cookie_prefix'] = \getenv('DRUPAL_SESSION_COOKIE_PREFIX');
} else {
  $settings['session_cookie_prefix'] = '__Host-';
}

/**
 * Services definition file names.
 *
 * These have the site path prepended lower down into the full path expected by
 * Drupal.
 */
$servicesYamls = [
  'default.services.yml',
  'services.session.yml',
  'services.security.yml',
];

/**
 * Additional services files, keyed by required environment variables.
 *
 * These are only included if their respective environment variables exist to
 * avoid fatal errors.
 *
 * @see https://www.drupal.org/project/drupal/issues/3249970
 *   Requires this Drupal core patch to use environment variables in service
 *   definitions and parameters.
 *
 * @todo Can we use Symfony environment variable processors to provide fallback
 *   values instead of doing this?
 *
 * @see https://symfony.com/doc/current/configuration/env_var_processors.html
 */
foreach ([
  'DRUPAL_HUX_OPTIMIZE'   => 'services.hux.yml',
  'DRUPAL_MONOLOG_EMAIL'  => 'services.monolog.yml',
  'DRUPAL_PRIMARY_HOST'   => 'services.routing.yml',
] as $env => $file) {

  if (\getenv($env) === false) {
    continue;
  }

  $servicesYamls[] = $file;

}

foreach ($servicesYamls as $fileName) {
  $settings['container_yamls'][] =
    $app_root . '/' . $site_path . '/' . $fileName;
}

/**
 * Trusted host configuration.
 *
 * This attempts to load the 'DRUPAL_PRIMARY_HOST' and 'DRUPAL_OEMBED_HOST'
 * environment variables, if they exist, and parses and escapes them before
 * adding them to the trusted host configuration. Note that this does not
 * attempt to parse them so they must contain only the domain name without any
 * scheme.
 *
 * Note that this is only enabled if not behind a trusted proxy, as it isn't of
 * much use in that case and the Trusted Reverse Proxy module will list this as
 * a warning.
 *
 * @see https://www.drupal.org/docs/installing-drupal/trusted-host-settings
 *
 * @see default.settings.php
 *   Contains full documentation for this setting.
 */
$settings['trusted_host_patterns'] = [];

if (\getenv('DRUPAL_REVERSE_PROXY_ENABLED') === false) {

  foreach ([
    'DRUPAL_PRIMARY_HOST',
    'DRUPAL_OEMBED_HOST',
  ] as $envName) {

    if (\getenv($envName) === false) {
      continue;
    }

    $settings['trusted_host_patterns'][] = '^' . \preg_quote(
      \getenv($envName)
    ) . '$';

  }

}

/**
 * Suppress emails and reroute them to logs if set in this environment.
 *
 * @see https://www.drupal.org/project/development_environment
 *   Uses the Development Environment module to accomplish this. Module must be
 *   installed in all environments.
 */
if (\getenv('DRUPAL_SUPPRESS_EMAIL') !== false) {
  $settings['development_environment.log_emails'] = true;
}

/**
 * SMTP hosts from environment variables.
 */
foreach ([
  'DRUPAL_SMTP_HOST_PRIMARY'    => 'smtp_host',
  'DRUPAL_SMTP_HOST_SECONDARY'  => 'smtp_hostbackup',
] as $envName => $configName) {

  if (\getenv($envName) === false) {
    continue;
  }

  $config['smtp.settings'][$configName] = \getenv($envName);

}

/**
 * The default list of directories that will be ignored by Drupal's file API.
 *
 * By default ignore node_modules and bower_components folders to avoid issues
 * with common frontend tools and recursive scanning of directories looking for
 * extensions.
 *
 * @see \Drupal\Core\File\FileSystemInterface::scanDirectory()
 * @see \Drupal\Core\Extension\ExtensionDiscovery::scanDirectory()
 */
$settings['file_scan_ignore_directories'] = [
  'node_modules',
  'bower_components',
];

/**
 * PHP storage location.
 *
 * These are set outside of the web root as that's more secure in case .htaccess
 * rules get borked.
 *
 * @see \Drupal\Core\PhpStorage\PhpStorageFactory
 */
$settings['php_storage']['twig']['directory'] = '../storage/php';
$settings['php_storage'][
  'html_purifier_serializer'
]['directory'] = '../storage/php';

/**
 * The default number of entities to update in a batch process.
 *
 * This is used by update and post-update functions that need to go through and
 * change all the entities on a site, so it is useful to increase this number
 * if your hosting configuration (i.e. RAM allocation, CPU speed) allows for a
 * larger number of entities to be processed in a single batch run.
 */
$settings['entity_update_batch_size'] = 50;

/**
 * Entity update backup.
 *
 * This is used to inform the entity storage handler that the backup tables as
 * well as the original entity type and field storage definitions should be
 * retained after a successful entity update process.
 */
$settings['entity_update_backup'] = true;

/**
 * Suppress PHP deprecation warnings.
 *
 * @see https://www.drupal.org/project/drupal/issues/1267246
 */
\error_reporting(\E_STRICT | \E_ALL & ~\E_DEPRECATED);

/**
 * Load local development override configuration, if available.
 */
if (\file_exists($app_root . '/' . $site_path . '/settings.local.php')) {
  require $app_root . '/' . $site_path . '/settings.local.php';
}

/**
 * Load DDEV settings file if this is a DDEV project.
 */
if (
  \getenv('IS_DDEV_PROJECT') === 'true' &&
  \file_exists($app_root . '/' . $site_path . '/settings.ddev.php')
) {
  require $app_root . '/' . $site_path . '/settings.ddev.php';
}
