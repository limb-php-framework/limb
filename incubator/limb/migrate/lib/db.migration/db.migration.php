#!/usr/bin/php
<?php
error_reporting(E_ALL);
ini_set("display_errors", "on");
ini_set("log_errors", "on");
ini_set("error_log", "/var/log/php_errors.log");

define('DB_MIGRATION_ROOT', dirname(__FILE__));
define('DB_MIGRATION_LIB',  dirname(__FILE__) . '/src');
define('DB_MIGRATION_CONFIG_MAIN', DB_MIGRATION_ROOT . '/migration.conf.php');
define('DB_MIGRATION_CONFIG_OVERRIDE', DB_MIGRATION_ROOT . '/migration.conf.override.php');

require_once(DB_MIGRATION_LIB . '/CliRunner.class.php');
require_once(DB_MIGRATION_LIB . '/DbMigration.class.php');

$cli_params = CliRunner::getParams($argv);
$command = CliRunner::getCommand($argv);

if(!$command || CliRunner::isUsageAsked($cli_params))
{
  CliRunner::showUsage();
  exit;
}

$params = CliRunner::getParamsFromConfig();
$params = CliRunner::applyCliParams($params, $cli_params);

$oMigration = new DbMigration($params['dsn'], $params['schema'], $params['data'], $params['migrations']);

CliRunner::runCommand($oMigration, $command, $params);