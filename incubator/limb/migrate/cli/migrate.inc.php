<?php

function prepare_project_params()
{
  lmb_package_require('config');
  lmb_package_require('fs');
  $db_conf = lmbToolkit::instance()->getConf('db');
  $migrate_conf = lmbToolkit::instance()->getConf('migrate');

  $params['dsn']    = $db_conf['dsn'];
  $params = $params + (array)$migrate_conf;
  lmbFs::mkdir(dirname($params['schema']));
  lmbFs::mkdir(dirname($params['data']));
  lmbFs::mkdir($params['migrations']);

  return $params;
}

/**
 * @return DbMigration
 */
function migrate_factory()
{
  require_once('limb/migrate/lib/db.migration/src/DbMigration.class.php');
  $hParams = prepare_project_params();
  return new DbMigration($hParams['dsn'], $hParams['schema'], $hParams['data'], $hParams['migrations']);
}

/**
 * @desc Setup default migrate.conf.php file to project settings
 * @example migrate_init_config
 */
function task_migrate_init_config($argv)
{
  lmb_package_require('config');
  lmb_package_require('fs');

  if(!lmbToolkit::instance()->hasConf('migrate'))
  {
     lmbFs::cp(
      taskman_prop('LIMB_DIR').'/limb/migrate/settings/migrate.conf.php.prototype',
      taskman_prop('PROJECT_DIR').'/settings/migrate.conf.php');
  }
}

/**
 * @desc Init migrations
 * @deps migrate_init_config
 * @example migrate_init
 */
function task_migrate_init($argv)
{
  $oMigration = migrate_factory();
  $iVersion = (int)lmb_cli_ask_for_option('Version number',1);
  $sIgnore = 'data';
  $oMigration->init($iVersion, $sIgnore);
}

/**
 * @desc Dump schema to file
 * @deps migrate_init_config
 * @example migrate_dump_schema
 */
function task_migrate_dump_schema($argv)
{
  $oMigration = migrate_factory();
  $sIgnore = 'data';
  $oMigration->dump($sIgnore);
}

/**
 * @desc Dump data to file
 * @deps migrate_init_config
 * @example migrate_dump_data
 */
function task_migrate_dump_data($argv)
{
  $oMigration = migrate_factory();
  $sIgnore = 'schema';
  $oMigration->dump($sIgnore);
}

/**
 * @desc Dump schema and data to files
 * @deps migrate_init_config
 * @example migrate_dump_all
 */
function task_migrate_dump_all($argv)
{
  $oMigration = migrate_factory();
  $oMigration->dump();
}

/**
 * @desc Load database schema and datas from files
 * @deps migrate_init_config
 * @example migrate_load_all
 */
function task_migrate_load_all($argv)
{
  $oMigration = migrate_factory();
  $oMigration->load();
}

/**
 * @desc Show changes in working DB.
 * @deps migrate_init_config
 * @example migrate_diff
 */
function task_migrate_diff($argv)
{
  $oMigration = migrate_factory();
  echo $oMigration->diff();
}

/**
 * @desc Create migration file
 * @deps migrate_init_config
 * @example migrate_create
 */
function task_migrate_create($argv)
{
  $oMigration = migrate_factory();
  $sName = (int)lmb_cli_ask_for_option('Migration name');
  $oMigration->createMigration($sName);
}

/**
 * @desc Apply new patches from migrations directory.
 * @deps migrate_init_config
 * @example migrate_run
 */
function task_migrate_run($argv)
{
  $oMigration = migrate_factory();
  $oMigration->migrate(false);
}

/**
 * @desc Test new patches from migrations directory.
 * @deps migrate_init_config
 * @example migrate_dryrun
 */
function task_migrate_dryrun($argv)
{
  $oMigration = migrate_factory();
  $oMigration->migrate(true);
}
