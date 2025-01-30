<?php

declare(strict_types=1);

$schema = \Drupal::service('database')->schema();

$tables = [
  'cache_permissions_by_term',
  'permissions_by_term_role',
  'permissions_by_term_user',
  'user__commerce_remote_id',
];

$commerceTables = $schema->findTables('commerce%');

$profileTables = $schema->findTables('profile%');

$tables = \array_merge(
  $tables, \array_values($commerceTables), \array_values($profileTables),
);

foreach ($tables as $name) {

  if ($schema->dropTable($name) === true) {

    \printf('Dropped "%s" table' . "\n", $name);

    continue;

  }

  \printf('Table "%s" was not found' . "\n", $name);

}
