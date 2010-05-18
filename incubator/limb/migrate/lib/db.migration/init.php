<?php

define('DB_MIGRATION_ROOT', dirname(__FILE__));
define('DB_MIGRATION_LIB',  dirname(__FILE__) . '/src');

require_once(DB_MIGRATION_LIB . '/DbMigration.class.php');


// simple user config realisation
$sMigrationConfFile = DB_MIGRATION_ROOT . '/migration.conf.php';
$sMigrationConfOverrideFile = DB_MIGRATION_ROOT . '/migration.conf.override.php';

// Including main config
if(file_exists($sMigrationConfFile) and is_readable($sMigrationConfFile))
  include_once($sMigrationConfFile);

if(!isset($migration_conf))
  $migration_conf = array();

// Including local config
if(file_exists($sMigrationConfOverrideFile) and is_readable($sMigrationConfOverrideFile))
  include_once($sMigrationConfOverrideFile);

if(isset($migration_conf['dsn']) and $migration_conf['dsn'])
{
	DbMigration::setDefaultDsn($migration_conf['dsn']);
}

// TOOLS


/**
 * Command Line Interface (CLI) utility.
 *
 * @author              Patrick Fisher <patrick@pwfisher.com>
 * @since               August 21, 2009
 * @filesource          http://pwfisher.com/nucleus/index.php?itemid=45
 */
function parseArgs($argv){
    array_shift($argv); $o = array();
    foreach ($argv as $a){
        if (substr($a,0,2) == '--'){ $eq = strpos($a,'=');
            if ($eq !== false){ $o[substr($a,2,$eq-2)] = substr($a,$eq+1); }
            else { $k = substr($a,2); if (!isset($o[$k])){ $o[$k] = true; } } }
        else if (substr($a,0,1) == '-'){
            if (substr($a,2,1) == '='){ $o[substr($a,1,1)] = substr($a,3); }
            else { foreach (str_split(substr($a,1)) as $k){ if (!isset($o[$k])){ $o[$k] = true; } } } }
        else { $o[] = $a; } }
    return $o;
}
