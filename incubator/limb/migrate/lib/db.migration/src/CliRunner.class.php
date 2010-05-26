<?php

class CliRunner
{
  static function getParams($argv)
  {
    return array_slice(self::parseArgs($argv), 1);
  }

  static function getCommand($argv)
  {
    return array_shift(self::parseArgs($argv));
  }

  static function getParamsFromConfig()
  {
    if(file_exists(DB_MIGRATION_CONFIG_MAIN) and is_readable(DB_MIGRATION_CONFIG_MAIN))
      require(DB_MIGRATION_CONFIG_MAIN);

    if(!isset($migration_conf))
      $migration_conf = array();

    if(file_exists(DB_MIGRATION_CONFIG_OVERRIDE) and is_readable(DB_MIGRATION_CONFIG_OVERRIDE))
      require(DB_MIGRATION_CONFIG_OVERRIDE);

    return $migration_conf;
  }

  static function applyCliParams($migration_conf, $args)
  {
    $hParams = array(
      'dsn' => null,
      'schema' => null,
      'data' => null,
      'migrations' => null,
    );
    foreach($hParams as $sParam=>$v)
    {
      if(isset($args[$sParam]) and $args[$sParam])
        $hParams[$sParam] = $args[$sParam];
      else
        $hParams[$sParam] = isset($migration_conf[$sParam]) ? $migration_conf[$sParam] : null;
    }

    return $hParams;
  }

  static function isUsageAsked($args)
  {
    return isset($args['h']) || isset($args['help']) || isset($args['usage']);
  }

  static function showUsage()
  {
    echo "PHP Migration tool. Usage:" . PHP_EOL .
      PHP_EOL .
      "php db.migration.php <command> " .
      "[--dsn=dsn_string] [--schema=file_path] [--data=file_path] [--migrations=dir_path]" .
      "[--name=<new_migration_name] [--version=ver_num] [--ignore=(schema|data)] [--test] " . PHP_EOL .
      "Commands:" . PHP_EOL .
      PHP_EOL .
      " init - Database initialization" . PHP_EOL .
      " dump - Dump data from --dsn to --schema, --data " . PHP_EOL .
      " load - Clean up --dsn and Load data from --schema, --data" . PHP_EOL .
      " diff - Show differnce between --dsn and --schema" . PHP_EOL .
      " create_migration - Create migration files (using diff) in --migrations, mark new schema version as UNIX_TIMESTAMP " . PHP_EOL .
      " migrate - Apply new patches from --migrations directory" . PHP_EOL .
      " config - Show configs paths and params" . PHP_EOL .
      " help|usage - Show this screen" . PHP_EOL .
      PHP_EOL .
      "DSN format: driver://[user][:password]@host[:port]/schema?param=value" . PHP_EOL .
      PHP_EOL .
      "All default variables could be set in migration.conf.php" . PHP_EOL .
      PHP_EOL;
  }

  static function runCommand($oMigration, $command, $params)
  {
    $bDryRun = isset($params['test']) ? true: false;

    echo 'DSN: ' . $params['dsn'] . PHP_EOL . PHP_EOL;

    switch($command)
    {
      case 'config':
        echo "Configs:" . PHP_EOL .
        "  Main: " . DB_MIGRATION_CONFIG_MAIN . PHP_EOL .
        "  Override: " . DB_MIGRATION_CONFIG_OVERRIDE . PHP_EOL;
        echo PHP_EOL;
        echo "Config params:" . PHP_EOL;
        foreach(self::getParamsFromConfig() as $name => $value)
          echo "  $name: $value" . PHP_EOL;
        echo PHP_EOL;
        echo "Actual params:" . PHP_EOL;
        foreach($params as $name => $value)
          echo "  $name: $value" . PHP_EOL;
        break;

      case 'dump':
        $ignore = isset($params['ignore']) ? $params['ignore']: null;
        $oMigration->dump($ignore);
        break;

      case 'init':
        $version = isset($params['version']) ? $params['version']: null;
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
        if(isset($params['name']) and $params['name'])
          $oMigration->createMigration($params['name']);
        else
          echo "create_migration requires --name=new_migration_name option" . PHP_EOL;
        break;

      default:
        echo PHP_EOL . "ERROR: Unknown command '$command'" . PHP_EOL . PHP_EOL;
        self::showUsage();
    }
  }

  /**
   * Command Line Interface (CLI) utility.
   *
   * @author        Patrick Fisher <patrick@pwfisher.com>
   * @since         August 21, 2009
   * @filesource      http://pwfisher.com/nucleus/index.php?itemid=45
   */
  protected static function parseArgs($argv)
  {
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
}