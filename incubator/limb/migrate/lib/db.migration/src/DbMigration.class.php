<?php

class DbMigration
{
  protected $_driver_dir;
  /**
   * @var MysqlDbDriver
   */
  protected $_db;
  protected $_dsn = array();
  protected $_schemaPath;
  protected $_dataPath;
  protected $_migrationsDir;
  static protected $_defaultDsn;

  /**
   * DataBase migration tool
   *
   * @param string $sDsn            DSN formatted string like "mysql://user:password@host.name:port/database_name?charset=UTF8"
   * @param string $sSchemaPath     Path to Schema file
   * @param string $sDataPath       Path to Data file
   * @param string $sMigrationsDir  Path to Migrations dir
   */
  public function __construct($sDsn, $sSchemaPath, $sDataPath, $sMigrationsDir)
  {
  	$this->_importDsn($sDsn);
  	$this->_schemaPath     = $sSchemaPath;
  	$this->_dataPath       = $sDataPath;
  	$this->_migrationsDir  = $sMigrationsDir;

  	$this->_loadDbDriver();
  }

  public function diff()
  {
    $this->_ensurePath($this->_schemaPath);
    $this->_ensurePath($this->_dataPath);
    $this->_ensurePath($this->_migrationsDir);

    return $this->getDb()->_diff($this->_dsn, $this->_schemaPath, $this->_dataPath, $this->_migrationsDir);
  }

  public function dump($ignore = null)
  {

    if (is_null($ignore) or 'schema'<>$ignore)
    {
      $this->_writeable($this->_schemaPath);
      $this->getDb()->_dump_schema($this->_dsn, $this->_schemaPath);
    }
    else
    {
      $ourFileHandle = fopen($this->_schemaPath, 'w') or die("can't open file");
      fclose($ourFileHandle);
    }

    if (is_null($ignore) or 'data'<>$ignore)
    {
      $this->_writeable($this->_dataPath);
      $this->getDb()->_dump_data($this->_dsn, $this->_dataPath);
    }
    else
    {
      $ourFileHandle = fopen($this->_dataPath, 'w') or die("can't open file");
      fclose($ourFileHandle);
    }
  }

  public function init($version = null, $ignore = null)
  {

    if (!$this->getDb()->_get_schema_version($this->_dsn))
      $version = 1;

    $this->getDb()->_set_schema_version($this->_dsn, $version);

    if (is_null($ignore) or 'schema'<>$ignore)
    {
      $this->_writeable($this->_schemaPath);
      $this->getDb()->_dump_schema($this->_dsn, $this->_schemaPath);
    }
    else
    {
      $ourFileHandle = fopen($this->_schemaPath, 'w') or die("can't open file");
      fclose($ourFileHandle);
    }

    if (is_null($ignore) or 'data'<>$ignore)
    {
      $this->_writeable($this->_dataPath);
      $this->getDb()->_dump_data($this->_dsn, $this->_dataPath);
    }
    else
    {
      $ourFileHandle = fopen($this->_dataPath, 'w') or die("can't open file");
      fclose($ourFileHandle);
    }
  }

  public function load()
  {
    $this->_ensurePath($this->_schemaPath);
    $this->_ensurePath($this->_dataPath);

    $this->getDb()->_db_cleanup($this->_dsn);

    $this->getDb()->_dump_load($this->_dsn, $this->_schemaPath);
    $this->getDb()->_dump_load($this->_dsn, $this->_dataPath);
  }

  public function migrate($bDryRun)
  {
    $this->_ensurePath($this->_schemaPath);
    $this->_ensurePath($this->_dataPath);
    $this->_ensurePath($this->_migrationsDir);

    if ($bDryRun)
    {
      $this->getDb()->_test_migration(
              $this->_dsn,
              $this->_schemaPath,
              $this->_dataPath,
              $this->_migrationsDir
      );
    }
    else
    {
      $this->getDb()->_migrate(
              $this->_dsn,
              $this->_migrationsDir
      );
    }
  }

  public function createMigration($sName)
  {
    $this->_ensurePath($this->_schemaPath);
    $this->_ensurePath($this->_dataPath);
    $this->_ensurePath($this->_migrationsDir);

    $this->_checkFileName($sName);

    $this->getDb()->_create_migration(
            $this->_dsn,
            $sName,
            $this->_schemaPath,
            $this->_dataPath,
            $this->_migrationsDir
    );
  }

  /**
   * Import DSN string
   * @param string $sDsn
   */
  public function _importDsn($sDsn)
  {
    if (!$sDsn)
      $sDsn = self::getDefaultDsn();

    $sDsn = trim($sDsn);

    if (!$hUrl = @parse_url($sDsn))
      throw new Exception("DSN '$sDsn' is not valid");

    $this->_dsn = array(
        'sDsn'=>$sDsn,
        'scheme'=>'',
        'host'=>'',
        'port'=>'',
        'password'=>'',
        'path'=>'',
        'query'=>array(),
        'charset'=>'utf8',
        'fragment'=>'',
    );

    foreach ($hUrl as $sKey=>$sValue)
    {
      switch ($sKey)
      {
        case 'scheme':
          $this->_dsn['protocol'] = $sValue;
          break;
        case 'host':
          $this->_dsn['host'] = $sValue;
          break;
        case 'port':
          $this->_dsn['port'] = $sValue;
          break;
        case 'user':
          $this->_dsn['user'] = $sValue;
          break;
        case 'pass':
          $this->_dsn['password'] = $sValue;
          break;
        case 'path':
          $this->_dsn['database'] = substr($sValue, 1);//removing first slash
          break;
        case 'query':
          parse_str($sValue, $this->_dsn['query']);
          if (isset($this->_dsn['query']['charset']))
            $this->_dsn['charset'] = $this->_dsn['query']['charset'];
          break;
        case 'fragment':
          $this->_dsn['fragment'] = $sValue;
          break;
      }
    }
  }

  /**
   * Load DbDriver
   */
  protected function _loadDbDriver()
  {
    $sDriverName = ucfirst(strtolower($this->_dsn['protocol']));

    $sClassName = $sDriverName.'DbDriver';
    $sClassPath = 'driver/'.$sClassName.'.class.php';
    $sIncludePath = realpath(dirname(__FILE__)).'/'.$sClassPath;

    if (!file_exists($sIncludePath))
    {
      throw new Exception("Couldn't load $sDriverName driver (must be in ./$sIncludePath)");
    }

    require_once($sIncludePath);

    $this->_db = new $sClassName($this->_dsn['sDsn'], null, $this->_schemaPath, $this->_dataPath, $this->_migrationsDir);
  }

  /**
   * Get DbDriver
   *
   * @return MysqlDbDriver
   */
  public function getDb()
  {
    return $this->_db;
  }

  protected function _ensurePath($sPath)
  {
    if (!is_file($sPath) and!is_dir($sPath))
      throw new Exception("Path '$sPath' does not exist");
  }

  protected function _checkFileName($sName)
  {
    if (!preg_match('/[a-zA-Z0-9_\-.]/', $sName))
      throw new Exception("File Name '$sName' must use only latin letters, diggits and _ - .");
  }

  protected function _writeable($sPath)
  {
    if (!@is_writeable($sPath) and!@is_writeable(dirname($sPath)))
      throw new Exception("Couldn't write to '$sPath'");
  }

  public static function setDefaultDsn($sDsn)
  {
    self::$_defaultDsn = $sDsn;
  }

  public static function getDefaultDsn()
  {
    return self::$_defaultDsn;
  }

}