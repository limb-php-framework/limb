<?php

/**
 *@always
 */
function task_entity_init_constructor()
{
  lmb_require('limb/constructor/src/lmbProjectConstructor.class.php');
  $override_files = taskman_propor('OVERRIDE', false);
  $project_constructor = new lmbProjectConstructor(taskman_prop('PROJECT_DIR'), $override_files);
  taskman_propset('CONSTRUCTOR', $project_constructor);
  taskman_sysmsg("Constructor initialized...\n");
}

function _filter_tables($tables, $filter)
{
  foreach ($tables as $key => $table)
    if(strstr($table->getName(), $filter))
    {
      taskman_msg('FILTER: table \'' . $table->getName() . '\' filtered by rule \''.$filter.'\''.PHP_EOL);
      unset($tables[$key]);
    }
  return $tables;
}

/**
 *@todo must be "always", but always dont receive args by default
 */
function task_entity_parse_table_argument($args)
{
  lmb_package_require('dbal');
  $database_info = lmbToolkit :: instance()->getDefaultDbConnection()->getDatabaseInfo();
  taskman_msg('DATABASE: '.$database_info->getName().PHP_EOL);

  $table_name = lmb_cli_ask_for_option("Table name (could be 'all')");

  if('all' !== $table_name)
    $tables = array($database_info->getTable($table_name));
  else
    $tables = _filter_tables($database_info->getTables(), 'lmb_');

  if(!count($tables))
  {
    taskman_sysmsg('No tables found in '.$table_name);
    exit(1);
  }

  taskman_propset('TABLES', $tables);
}

function _createAndRunConstructor($constructor_name)
{
  lmb_package_require('dbal');
  $database_info = lmbToolkit :: instance()->getDefaultDbConnection()->getDatabaseInfo();

  foreach(taskman_prop('TABLES') as $table)
  {
    $templates_dir = lmbToolkit::instance()->getConf('constructor')->get('templates_dir');
    $constructor = new $constructor_name(
      taskman_prop('CONSTRUCTOR'),
      $database_info,
      $table,
      null, // By default model name same as table name
      $templates_dir
    );
    $constructor->create();
    taskman_msg('CONSTRUCTOR: ' . $constructor_name . ' on table ' . $table->getName() . PHP_EOL);
  }
}

/**
 * @desc create model specified by table name
 * @param table_name|all
 */
function task_entity_create_model()
{
  lmb_require('limb/constructor/src/lmbModelConstructor.class.php');
  _createAndRunConstructor('lmbModelConstructor');
}

/**
 * @desc create front controller and front templates for entity specified by table name
 * @param table_name|all
 * @deps entity_parse_table_argument
 */
function task_entity_create_front()
{
  lmb_require('limb/constructor/src/lmbFrontStuffConstructor.class.php');
  _createAndRunConstructor('lmbFrontStuffConstructor');
}

/**
 * @desc create admin controller and admin templates for entity specified by table name
 * @param table_name|all
 * @deps entity_parse_table_argument
 */
function task_entity_create_admin()
{
  lmb_require('limb/constructor/src/lmbAdminControllerConstructor.class.php');
  _createAndRunConstructor('lmbAdminControllerConstructor');
  lmb_require('limb/constructor/src/lmbAdminTemplatesConstructor.class.php');
  _createAndRunConstructor('lmbAdminTemplatesConstructor');
}

/**
 * @desc create model, front and admin controllers, front and admin templates for entity specified by table name
 * @param table_name|all
 * @deps entity_parse_table_argument,entity_create_model,entity_create_front,entity_create_admin
 */
function task_entity_create() {}

/**
 * @desc create model, front and admin controllers, front and admin templates for tree entity specified by table name
 * @param table_name
 * @deps entity_parse_table_argument
 */
function task_entity_create_tree()
{
  _createAndRunConstructor('lmbTreeModelConstructor');
  _createAndRunConstructor('lmbFrontStuffConstructor');
  _createAndRunConstructor('lmbAdminTreeControllerConstructor');
  _createAndRunConstructor('lmbAdminTreeTemplatesConstructor');
}
