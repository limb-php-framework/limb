<?php

/*function task_usage()
{
  $version = file_get_contents(taskman_prop('PROJECT_DIR').'/lib/limb/constructor/VERSION');
  $help = <<<EOD
Limb project constructor. Version $version
Usage: project.php COMMAND [-D DSN=DSN]

Commands:
\tshares\t\t\tcopy packages shares to PROJECT/www/shared
\tvar_dir\t\t\tcreate var directory
\tinit_db_config \t\tinit db config by given DSN param
\tinit_db \t\tinit database
\tinit\t\t\tfull init skel

EOD;
  echo $help;
  exit(0);
}*/

/**
 * @desc Create folders in /www/shared for all packages
 */
function task_shares()
{  
  foreach(glob(taskman_prop('PROJECT_DIR')."/lib/limb/*/shared") as $pkg_shared)
  {
    $pkg = basename(dirname($pkg_shared));

    $shared_dir = taskman_prop('PROJECT_DIR') . '/www/shared/';
    if(!is_dir($shared_dir)) mkdir($shared_dir,0755,true);
    $destination = $shared_dir . $pkg;

    lmbFs::rm($destination);

    if(function_exists('symlink'))
    {
      symlink($pkg_shared, $destination);
      taskman_msg("Created symlink for $pkg ($destination)...\n");
    }
    else
    {
      lmbFs::cp($pkg_shared, $destination);
      taskman_msg("Copied share for $pkg ($destination)...\n");
    }
  }
}

/**
 * @desc Create var folder
 */
function task_var_dir()
{
  $var_path = taskman_prop('PROJECT_DIR') . '/var';
  lmbFs::mkdir($var_path);
  taskman_msg("Created var dir ($var_path)...\n");
}

/**
 *@desc Init db config by given DSN param
 *@example project.php init_db_config -D DSN=sqlite:///www/skel/db/database.sqlite
 */
function task_init_db_config()
{
  $dsn = taskman_propor('DSN', '');
  if(!$dsn)
  {
    taskman_sysmsg("ERROR: DSN prop is required and must be valid DSN string\n");
    exit(1);
  }

  $config_text = <<<EOD
<?php

\$conf = array('dsn' => '$dsn');
EOD;
  $config_file = taskman_prop('PROJECT_DIR').'/settings/db.conf.php';
  file_put_contents($config_file, $config_text);
  taskman_msg("DB config ($config_file) writed...\n");
}

/**
 * @desc Init db
 * @deps init_db_config
 * @example project.php init_db -D DSN=mysqli://root:test@localhost/limb_app?charset=utf8
 */
function task_init_db($argv)
{
  require_once('limb/dbal/src/lmbDbDump.class.php');

  $type = lmbToolkit :: instance()->getDefaultDbConnection()->getType();
  $dump_file = taskman_prop('PROJECT_DIR') . '/lib/limb/'.taskman_propor('INIT_PACKAGE','cms'). '/init/db.' . $type;
  $dump = new lmbDbDump($dump_file);
  $dump->load();
  taskman_msg("Dump ($dump_file) loaded...\n");
}



/**
 * @desc create new project in specified path
 * @param path
 * @example project.php create_project /var/www/example-limb-project.com
 */
function task_install($args)
{

  if(!count($args)) throw new Exception('path to install not specified');
  $path = $args[0];
  if(file_exists($path) && !is_dir($path)) throw new Exception('path is not dir');
  if(!is_dir($path))
  {
    mkdir($path.DIRECTORY_SEPARATOR.'www', 0755, true);
    mkdir($path.DIRECTORY_SEPARATOR.'lib', 0755, true);
  }
  lmb_require('limb/fs/src/lmbFs.class.php');
  lmbFs::cp(realpath(taskman_prop('PROJECT_DIR')).'/lib/limb', $path.'/lib/limb');
  
  //Temporary, while constructor in incubator
  lmbFs::cp( realpath(taskman_prop('PROJECT_DIR')).'/lib/incubator/limb/constructor',
             $path.'/lib/limb/constructor');

  lmbFs::cp($path.'/lib/limb/constructor/cli',$path.'/cli');
  lmbFs::cp($path.'/lib/limb/web_app/skel',$path);
}



/**
 * @desc create db by specified DSN
 * @example project.php create_db -D DSN=mysqli://root:test@localhost/limb_app?charset=utf8
 */
function task_create_db($args)
{
  $dsn = taskman_propor('DSN', '');
  if(!$dsn)
  {
    taskman_sysmsg("ERROR: DSN prop is required and must be valid DSN string\n");
    exit(1);
  }
  
  $temp = lmbToolkit :: instance()->castToDsnObject($dsn)->export();
  $db = $temp['database'];
  $temp['database'] = false;
  $DSN2 = new lmbDbDSN($temp);
  lmbToolkit :: instance()->setDefaultDbDSN($DSN2);
  $conn = lmbToolkit::instance()->getDefaultDbConnection();
  $conn->connect();
  $conn->execute('CREATE DATABASE '.$conn->quoteIdentifier($db));

}

/**
 * @desc init cms installation
 * @example project.php init_cms
 */
function task_init_cms($args)
{
  $application =
    '<?php'."\n".
    'lmb_require(\'limb/cms/src/lmbCmsApplication.class.php\');'."\n".
    "\n".
    '/**'."\n".
    ' * class LimbApplication.'."\n".
    ' *'."\n".
    ' * @package web_app'."\n".
    ' */'."\n".
    'class LimbApplication extends lmbCmsApplication'."\n".
    '{'."\n".
    '  /*function __construct()'."\n".
    '  {'."\n".
    '    //register your own custom filter chain here'."\n".
    '  }'."\n".
    '  */'."\n".
    '}';
  $setup =
    '<?php'."\n".
    '/*'."\n".
    ' * Limb PHP Framework'."\n".
    ' *'."\n".
    ' * @link http://limb-project.com'."\n".
    ' * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)'."\n".
    ' * @license    LGPL http://www.gnu.org/copyleft/lesser.html'."\n".
    ' */'."\n".
    "\n".
    'set_include_path(implode(PATH_SEPARATOR,'."\n".
    '  array('."\n".
    '    dirname(__FILE__),'."\n".
    '    dirname(__FILE__) . \'/lib/\','."\n".
    '    get_include_path()'."\n".
    '  )'."\n".
    '));'."\n".
    "\n".
    'if(file_exists(dirname(__FILE__) . \'/setup.override.php\'))'."\n".
    '  require_once(dirname(__FILE__) . \'/setup.override.php\');'."\n".
    "\n".
    'require_once(\'limb/core/common.inc.php\');'."\n".
    'require_once(\'limb/cms/common.inc.php\');'."\n".
    "\n".
    'lmb_env_setor(\'LIMB_VAR_DIR\', dirname(__FILE__) . \'/var/\');'."\n";

  $root = taskman_prop('PROJECT_DIR');
  file_put_contents($root.'/src/LimbApplication.class.php', $application);
  file_put_contents($root.'/setup.php', $setup);
}

/**
 * @desc Init project meta task
 * @deps shares,var_dir,init_db
 * @example project.php init -D DSN=mysqli://root:test@localhost/limb_app?charset=utf8
 */
function task_init($argv)
{

}
