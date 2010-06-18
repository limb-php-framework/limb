#!/usr/bin/php
<?php
error_reporting(E_ALL);
ini_set("display_errors", "on");
ini_set("log_errors", "on");
ini_set("error_log", "/var/log/php_errors.log");

define('DB_MIGRATION_ROOT', dirname(__FILE__));
define('DB_MIGRATION_LIB',  dirname(__FILE__) . '/src');

require_once(DB_MIGRATION_LIB . '/CliRunner.class.php');

$runner = new CliRunner();
$runner->setCliParams($argv);
$runner->applyParamsFromFile(DB_MIGRATION_ROOT . '/migration.conf.override.php');
$runner->applyParamsFromFile(DB_MIGRATION_ROOT . '/migration.conf.php');

if(!$runner->IsCommandAsked() || $runner->isUsageAsked())
{
  $runner->showUsage();
  exit;
}

$runner->run();
