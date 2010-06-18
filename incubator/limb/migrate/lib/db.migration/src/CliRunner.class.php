<?php

require_once(DB_MIGRATION_LIB . '/DbMigration.class.php');

class CliRunner
{
  protected $command;
  protected $params = array();

  function applyParamsFromFile($file_name)
  {
    if(!file_exists($file_name))
    {
      echo "NOTICE: Params file '{$file_name}' not found.\n";
      return;
    }
    require_once($file_name);
    if(!isset($migration_conf) || !is_array($migration_conf))
    {
      echo "ERROR: Params file '{$file_name}' have no \$migration_conf array.\n";
      return;
    }
    $this->params = $this->params + $migration_conf;
  }

  function setCliParams(array $argv)
  {
    $this->command = array_shift(self::parseArgs($argv));
    $this->params = array_slice(self::parseArgs($argv), 1);
  }

  function isCommandAsked()
  {
    return (bool) $this->command;
  }

  function isUsageAsked()
  {
    return isset($this->params['h']) || isset($this->params['help']) || isset($this->params['usage']);
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

  function run()
  {
    $params = $this->params;

    $migrator = new DbMigration($params['dsn'], $params['schema'], $params['data'], $params['migrations']);

    $bDryRun = isset($params['test']) ? true: false;

    echo 'DSN: ' . $params['dsn'] . PHP_EOL;

    switch($this->command)
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
        $migrator->dump($ignore);
        break;

      case 'init':
        $version = isset($params['version']) ? $params['version']: null;
        $migrator->init($version);
        break;

      case 'load':
        $migrator->load();
        break;

      case 'diff':
        echo $migrator->diff();
        break;

      case 'migrate':
        $migrator->migrate($bDryRun);
        break;

      case 'create_migration':
        if(isset($params['name']) and $params['name'])
          $migrator->createMigration($params['name']);
        else
          echo "create_migration requires --name=new_migration_name option" . PHP_EOL;
        break;

      default:
        echo PHP_EOL . "ERROR: Unknown command '{$this->command}'" . PHP_EOL . PHP_EOL;
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
  static function parseArgs($argv)
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