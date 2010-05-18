#!/usr/bin/php
<?php
require_once(dirname(__FILE__) . '/init.php');

error_reporting(E_ALL);
ini_set("display_errors", "on");
ini_set("log_errors", "on");
ini_set("error_log", "/var/log/php_errors.log");

// CLI arguments
$arg = parseArgs($argv);

// Usage page
$sUsage =
  "PHP Migration tool. Usage:" . PHP_EOL .
  "php db.migration.php <command> " .
  "[--dsn=dsn_string] [--schema=file_path] [--data=file_path] [--migrations=dir_path]" .
  "[--name=<new_migration_name] [--version=ver_num] [--test] " . PHP_EOL .
  "Commands:" . PHP_EOL .
  " dump - Dump data from --dsn to --schema, --data " . PHP_EOL .
  " load - Clean up --dsn and Load data from --schema, --data" . PHP_EOL .
  " diff - Show differnce between --dsn and --schema" . PHP_EOL .
  " create_migration - Create migration files (using diff) in --migrations, mark new schema version as UNIX_TIMESTAMP " . PHP_EOL .
  " migrate - Apply new patches from --migration" . PHP_EOL .
  "DSN format:" . PHP_EOL .
  " protocol://user:password@host:port/schema?param=value" . PHP_EOL .
  "All default variables could be set in migration.conf.php" . PHP_EOL .
  "";

if($argc < 2 || isset($arg['h']) || isset($arg['help']) || isset($arg['usage']))
{
	echo $sUsage;
  exit;
}

// Params
$hParams = array(
            'dsn' => null,
            'schema' => null,
            'data' => null,
            'migrations' => null,
          );
foreach($hParams as $sParam=>$v)
{
	if(isset($arg[$sParam]) and $arg[$sParam])
	  $hParams[$sParam] = $arg[$sParam];
	else
	  $hParams[$sParam] = isset($migration_conf[$sParam]) ? $migration_conf[$sParam]:null;
}

$bDryRun = false;
if(isset($arg['test']))
  $bDryRun = true;


// Main logic
$oMigration = new DbMigration($hParams['dsn'], $hParams['schema'], $hParams['data'], $hParams['migrations']);

switch($arg[0])
{
	case 'dump':
		$oMigration->dump();
		break;
	case 'init':
	  $version = isset($arg['version']) ? $arg['version']: null;
		$oMigration->init($version);
		break;
	case 'load':
		$oMigration->load();
		break;
	case 'diff':
		echo $oMigration->diff();
		break;
	case 'migrate':
		$oMigration->migrate($bDryRun);
		break;
	case 'create_migration':
		if(isset($arg['name']) and $arg['name'])
		  $oMigration->createMigration($arg['name']);
		else
		  echo "create_migration requires --name=new_migration_name option" . PHP_EOL;
		break;
}