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
 * Enable the Paranoia configuration split by default.
 *
 * Note that this will be ignored if the 'DRUPAL_CONFIG_SPLITS' environment
 * variable has been set as that takes priority.
 *
 * @see https://www.drupal.org/project/config_split/issues/3109103
 *   Configuration Split issue explaining why a separate split is necessary.
 *
 * @todo Remove this completely when we're able to set environment variables in
 *   all our hosting environments.
 */
if (\getenv('DRUPAL_CONFIG_SPLITS') === false) {
  $config['config_split.config_split.paranoia']['status'] = true;
}

/**
 * Salt for one-time login links, cancel links, form tokens, etc.
 *
 * This first tries to use the 'DRUPAL_HASH_SALT' environment variable if it
 * exists, falling back to a file-based key in ../keys/drupal_hash_salt.key if
 * it exists.
 */
if (\getenv('DRUPAL_HASH_SALT') !== false) {

  $settings['hash_salt'] = \getenv('DRUPAL_HASH_SALT');

} else if (\file_exists($app_root . '/../keys/drupal_hash_salt.key')) {

  $settings['hash_salt'] = \file_get_contents(
    $app_root . '/../keys/drupal_hash_salt.key'
  );

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

foreach ($servicesYamls as $fileName) {
  $settings['container_yamls'][] =
    $app_root . '/' . $site_path . '/' . $fileName;
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
