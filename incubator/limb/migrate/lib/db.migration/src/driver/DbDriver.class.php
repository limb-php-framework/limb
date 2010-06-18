<?php

abstract class DbDriver {

  protected $_dsn = array();

  protected $_dsnOut = array();

  protected $_schemaPath;

  protected $_dataPath;

  protected $_migrationsDir;

  protected $_conn;

  protected $_tmpSchema = '/tmp/tmp_migration_schema';
  /**
   * DataBase migration driver
   *
   * @param string $sDsn            DSN formatted string like "mysql://user:password@host.name:port/database_name?charset=UTF8"
   * @param string $sSchemaPath     Path to Schema file
   * @param string $sDataPath       Path to Data file
   * @param string $sMigrationsDir  Path to Migrations dir
   */
  public function __construct($sDsn, $sDsnOut, $sSchemaPath, $sDataPath, $sMigrationsDir)
  {
    $this->_dsn     = $this->_importDsn($sDsn);
    $this->_dsnOut  = $this->_importDsn($sDsnOut);

    $this->_schemaPath     = $sSchemaPath;
    $this->_dataPath       = $sDataPath;
    $this->_migrationsDir  = $sMigrationsDir;

  }

  protected function _log($sMsg)
  {
  	echo $sMsg;
  }
  /**
   * Import DSN string
   * @param string $sDsn
   */
  protected function _importDsn($sDsn)
  {
    $hDsn = array(
                    'sDsn' => $sDsn,
                    'scheme' => '',
                    'host' => '',
                    'port' => '',
                    'password' => '',
                    'path' => '',
                    'query' => array(),
                    'charset' => 'utf8',
                    'fragment' => '',
                    );

    if(!$sDsn)
      return $hDsn;

   if(!$hUrl = @parse_url($sDsn))
      throw new Exception("DSN '$sDsn' is not valid");

    foreach($hUrl as $sKey => $sValue)
    {
      switch($sKey)
      {
        case 'scheme':
          $hDsn['protocol'] = $sValue;
          break;
        case 'host':
          $hDsn['host'] = $sValue;
          break;
        case 'port':
          $hDsn['port'] = $sValue;
          break;
        case 'user':
          $hDsn['user'] = $sValue;
          break;
        case 'pass':
          $hDsn['password'] = $sValue;
          break;
        case 'path':
          $hDsn['database'] = substr($sValue, 1);//removing first slash
          break;
        case 'query':
          parse_str($sValue, $hDsn['query']);
          if(isset($hDsn['query']['charset']))
           $hDsn['charset'] = $hDsn['query']['charset'];
          break;
        case 'fragment':
          $hDsn['fragment'] = $sValue;
          break;
      }
    }

    return $hDsn;
  }

  abstract function _connect_string($dsn);

  abstract function _nondb_exec($dsn, $cmd);

  abstract function _exec($dsn, $cmd);

  abstract function _load($dsn, $file);

  abstract function _db_exists($dsn);

  abstract function _table_exists($dsn, $table);

  abstract function _get_tables($dsn);

  abstract function _create_tmp_db($dsn);

  abstract function _db_drop($dsn);

  abstract function _dump_schema($dsn, $file);

  abstract function _dump_data($dsn, $file);

  abstract function _dump_load($dsn, $file);

  abstract function _copy_schema($dsn_src, $dsn_dst);

  abstract function _copy_schema_and_use_memory_engine($dsn_src, $dsn_dst);

  function _db_cleanup($dsn)
  {
    extract($dsn);

    $tables = $this->_get_tables($dsn);
    $this->_drop_tables($dsn, $tables);

    $this->_log("Starting clean up of '$database' DB...\n");

    $this->_log("done\n");
  }

  abstract function _drop_tables($dsn, $tables);

  abstract function _truncate_tables($dsn, $tables);

  function _get_migration_files_since($migrations_dir, $base_version)
  {
    $files = array();
    foreach(glob($migrations_dir . '/*') as $file)
    {
      list($version, ) = explode('_', basename($file));
      $version = intval($version);
      if($version > $base_version)
        $files[$version] = $file;
    }
    ksort($files);
    return $files;
  }

  function _get_last_migration_file($migrations_dir)
  {
    $files = glob($migrations_dir . '*');
    krsort($files);
    return reset($files);
  }

  abstract function _migrate($dsn, $migrations_dir, $since = null);

  abstract function _get_schema_version($dsn);

  abstract function _set_schema_version($dsn, $since = null);

  function _test_migration($dsn, $sql_schema, $sql_data, $migrations_dir)
  {
    extract($dsn);
	$this->_log("===== Testing migration of DB(dry-run) =====\n");

	$tmp_db = $this->_create_tmp_db($dsn);
	$dsn['database'] = $tmp_db;

	// getting version of loaded schema
	$since = $this->_get_schema_version($dsn);

	$this->_dump_load($dsn, $this->_schemaPath);
//	$this->_load($dsn, $this->_tmpSchema);
	try
	{
	  $this->_migrate($dsn, $migrations_dir, $since);
	}
	catch(Exception $e)
	{
	  $this->_log("\nWARNING: migration error:\n" . $e->getMessage());
	  $this->_log("\nPlease correct the migration\n");
	  $this->_db_drop($dsn);
	  return false;
	}
	$this->_db_drop($dsn);
	$this->_log("Everything seems to be OK\n");
    return true;
  }

  abstract function _copy_tables_contents($dsn_src, $dsn_dst, $tables);
}