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
 * Load services definition file.
 */
$settings['container_yamls'][] = $app_root . '/' . $site_path . '/services.yml';

// Only include the Monolog services file if not running tests, by checking if
// one of the test environment variables is set. This is necessary as Symfony
// will throw an error about requesting a non-existent parameter
// "monolog.level.notice". Not that when running tests via core's run-tests.sh,
// core's phpunit.xml.dist will be used so it isn't easy to set a custom
// environment variable that way.
//
// @see https://www.php.net/manual/en/function.getenv.php
//
// @todo What if Monolog services are needed by a test?
if (\getenv('SIMPLETEST_BASE_URL') === false) {
// if (!\getenv('SETTINGS_PHP_EXCLUDE_MONOLOG')) {
  $settings['container_yamls'][] = $app_root . '/' . $site_path . '/monolog.services.yml';
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
