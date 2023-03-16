<?php

// phpcs:ignoreFile

/**
 * @file
 * Customized and stripped down Drupal configuration file.
 *
 * @see default.settings.php
 *   Contains full documentation.
 */

/**
 * Database settings:
 *
 * The $databases array specifies the database connection or
 * connections that Drupal may use.  Drupal is able to connect
 * to multiple databases, including multiple types of databases,
 * during the same request.
 *
 * One example of the simplest connection array is shown below. To use the
 * sample settings, copy and uncomment the code below between the @code and
 * @endcode lines and paste it after the $databases declaration. You will need
 * to replace the database username and password and possibly the host and port
 * with the appropriate credentials for your database system.
 *
 * The next section describes how to customize the $databases array for more
 * specific needs.
 *
 * @code
 * $databases['default']['default'] = [
 *   'database' => 'databasename',
 *   'username' => 'sqlusername',
 *   'password' => 'sqlpassword',
 *   'host' => 'localhost',
 *   'port' => '3306',
 *   'driver' => 'mysql',
 *   'prefix' => '',
 *   'collation' => 'utf8mb4_general_ci',
 * ];
 * @endcode
 */
if (
  file_exists($app_root . '/' . $site_path . '/settings.database.php')
) {
  include $app_root . '/' . $site_path . '/settings.database.php';

// Check for DigitalOcean App Platform database environment variables.
} else if (
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

}

/**
 * Set the MySQL transaction isolation level as recommended by Drupal.
 *
 * @see https://www.drupal.org/docs/system-requirements/setting-the-mysql-transaction-isolation-level
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
 * Enable the Paranoia configuration split by default.
 *
 * @see https://www.drupal.org/project/config_split/issues/3109103
 *   Configuration Split issue explaining why a separate split is necessary.
 */
$config['config_split.config_split.paranoia']['status'] = true;

/**
 * Salt for one-time login links, cancel links, form tokens, etc.
 *
 * This variable will be set to a random value by the installer. All one-time
 * login links will be invalidated if the value is changed. Note that if your
 * site is deployed on a cluster of web servers, you must ensure that this
 * variable has the same value on each server.
 *
 * For enhanced security, you may set this variable to the contents of a file
 * outside your document root; you should also ensure that this file is not
 * stored with backups of your database.
 *
 * Example:
 * @code
 *   $settings['hash_salt'] = file_get_contents('/home/example/salt.txt');
 * @endcode
 */
if (file_exists($app_root . '/../keys/drupal_hash_salt.key')) {

  $settings['hash_salt'] = \file_get_contents(
    $app_root . '/../keys/drupal_hash_salt.key'
  );

// Fall back to the the environment variable if it exists.
} else if (\getenv('DRUPAL_HASH_SALT') !== false) {
  $settings['hash_salt'] = \getenv('DRUPAL_HASH_SALT');
}

// Quick and dirty two factor secret via environment variable if available.
//
// @todo Move this to a config split?
if (\getenv('DRUPAL_TWO_FACTOR_SECRET') !== false) {

  $config['encrypt.profile.two_factor']['encryption_key'] =
    'two_factor_secret_environment';

  $config['encrypt.profile.two_factor']['dependencies']['config'] = [
    'key.key.two_factor_secret_environment'
  ];

}

/**
 * Access control for update.php script.
 *
 * If you are updating your Drupal installation using the update.php script but
 * are not logged in using either an account with the "Administer software
 * updates" permission or the site maintenance account (the account that was
 * created during installation), you will need to modify the access check
 * statement below. Change the FALSE to a TRUE to disable the access check.
 * After finishing the upgrade, be sure to open this file again and change the
 * TRUE back to a FALSE!
 */
$settings['update_free_access'] = FALSE;

/**
 * Primary host name if set via the 'DRUPAL_PRIMARY_HOST' environment variable.
 */
if (\getenv('DRUPAL_PRIMARY_HOST') !== false) {
  $settings['primary_host'] = \getenv('DRUPAL_PRIMARY_HOST');
}

/**
 * Public file path:
 *
 * A local file system path where public files will be stored. This directory
 * must exist and be writable by Drupal. This directory must be relative to
 * the Drupal installation directory and be accessible over the web.
 */
$settings['file_public_path'] = 'sites/default/files';

/**
 * Optimized assets path:
 *
 * A local file system path where optimized assets will be stored. This
 * directory must exist and be writable by Drupal. This directory must be
 * relative to the Drupal installation directory and be accessible over the
 * web.
 */
$settings['file_assets_path'] = 'assets';

/**
 * Private file path:
 *
 * A local file system path where private files will be stored. This directory
 * must be absolute, outside of the Drupal installation directory and not
 * accessible over the web.
 *
 * Note: Caches need to be cleared when this value is changed to make the
 * private:// stream wrapper available to the system.
 *
 * See https://www.drupal.org/documentation/modules/file for more information
 * about securing private files.
 */
$settings['file_private_path'] = \realpath(
  $app_root . '/../drupal_private_files'
);

/**
 * Enable S3 File System public/private if DigitalOcean Spaces env var found.
 *
 * The public and private file paths should still be set above regardless so
 * Drupal won't complain. The S3 module will still override them when the below
 * are active.
 */
if (\getenv('SPACES_ACCESS') !== false) {
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
 * This uses the '__Host-' prefix for security hardening.
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
$settings['session_cookie_prefix'] = '__Host-';

/**
 * Services definition file names.
 *
 * These have the site path prepended lower down into the full path expected by
 * Drupal.
 */
$servicesYamls = [
  'default.services.yml',
  'services.security.yml',
];

// Only include the Monolog services file if not running tests, by checking if
// one of the test environment variables is set. This is necessary as Symfony
// will throw an error about requesting a non-existent parameter
// "monolog.level.notice". Not that when running tests via core's run-tests.sh,
// core's phpunit.xml.dist will be used so it isn't easy to set a custom
// environment variable that way.
//
// @todo What if Monolog services are needed by a test?
if (\getenv('SIMPLETEST_BASE_URL') === false) {
  $servicesYamls[] = 'services.monolog.yml';
}

if (\getenv('DRUPAL_PRIMARY_HOST') !== false) {
  $servicesYamls[] = 'services.routing.yml';
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
$settings['entity_update_backup'] = TRUE;

/**
 * Suppress PHP deprecation warnings.
 *
 * @see https://www.drupal.org/project/drupal/issues/1267246
 */
\error_reporting(\E_STRICT | \E_ALL & ~\E_DEPRECATED);

/**
 * Load local development override configuration, if available.
 *
 * Use settings.local.php to override variables on secondary (staging,
 * development, etc) installations of this site. Typically used to disable
 * caching, JavaScript/CSS compression, re-routing of outgoing emails, and
 * other things that should not happen on development and testing sites.
 *
 * Keep this code block at the end of this file to take full effect.
 */
if (
  file_exists($app_root . '/' . $site_path . '/settings.local.php')
) {
  include $app_root . '/' . $site_path . '/settings.local.php';
}

// Automatically generated include for settings managed by ddev.
$ddev_settings = dirname(__FILE__) . '/settings.ddev.php';
if (getenv('IS_DDEV_PROJECT') == 'true' && is_readable($ddev_settings)) {
  require $ddev_settings;
}
