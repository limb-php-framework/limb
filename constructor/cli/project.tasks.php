<?php

/**
 * @desc Init project meta task
 * @deps project_files,project_shares,project_var_dir,project_init_cms,project_db_load
 * @example project.php init -D DSN=mysqli://root:test@localhost/limb_app?charset=utf8
 */
function task_project_create($argv)
{
}

/**
 * @desc Create new project in specified path
 * @param path
 * @example project.php create_project /var/www/example-limb-project.com
 */
function task_project_files($args)
{
  $proj_dir = taskman_prop('PROJECT_DIR');
  $limb_dir = taskman_prop('LIMB_DIR');

  if(file_exists($proj_dir) && !is_dir($proj_dir))
    throw new Exception('Path is not a dir');

  lmb_require('limb/fs/src/lmbFs.class.php');

  if(!is_dir($proj_dir))
  {
    lmbFs::mkdir($proj_dir . '/www', 0755, true);
    taskman_msg("Created dir $proj_dir/www ...Done\n");
    lmbFs::mkdir($proj_dir . '/lib', 0755, true);
    taskman_msg("Created dir $proj_dir/lib ...Done\n");
  }

  taskman_msg('Limb is copied...');
  lmbFs::cp($limb_dir . '/limb', $proj_dir . '/lib/limb');
  taskman_msg("Done\n");

  taskman_msg('Skel is copied...');
  lmbFs::cp($proj_dir.'/lib/limb/web_app/skel', $proj_dir);
  taskman_msg("Done\n");
}

/**
 * @desc init cms installation
 * @example project.php init_cms
 */
function task_project_init_cms($args)
{
  $application = <<<EOD
<?php
lmb_require('limb/cms/src/lmbCmsApplication.class.php');

class LimbApplication extends lmbCmsApplication
{
  /*function __construct()
  {
    //register your own custom filter chain here
  }
 */
}

EOD;
  $setup = <<<EOD
<?php

set_include_path(implode(PATH_SEPARATOR,
  array(
    dirname(__FILE__) . '/lib/',
    dirname(__FILE__),
    get_include_path()
  )
));

require_once('limb/core/common.inc.php');

if(file_exists(dirname(__FILE__) . '/setup.override.php'))
  require_once(dirname(__FILE__) . '/setup.override.php');

lmb_package_require('cms');

lmb_env_setor('LIMB_VAR_DIR', dirname(__FILE__) . '/var/');
lmb_env_setor('LIMB_APP_MODE' , 'production');
EOD;

  $root = taskman_prop('PROJECT_DIR');
  file_put_contents($root.'/src/LimbApplication.class.php', $application);
  file_put_contents($root.'/setup.php', $setup);
}

/**
 * @desc Create folders in /www/shared for all packages
 */
function task_project_shares()
{
  lmb_require('limb/fs/src/lmbFs.class.php');
  foreach(glob(taskman_prop('PROJECT_DIR')."/lib/limb/*/shared") as $pkg_shared)
  {
    $pkg = basename(dirname($pkg_shared));

    $shared_dir = taskman_prop('PROJECT_DIR') . '/www/shared/';
    if(!is_dir($shared_dir)) mkdir($shared_dir,0755,true);
    $destination = $shared_dir . $pkg;

    if(is_link($destination))
      unlink($destination);
    else
      lmbFs::rm($destination);

    try {
      if(function_exists('symlink'))
      {
        symlink($pkg_shared, $destination);
        taskman_msg("Created symlink for $pkg ($destination)...\n");
      }
      else
        throw new Exception();
    }
    // case: mounted FS doesn't support symlink
    catch (Exception $e)
    {
      lmbFs::cp($pkg_shared, $destination);
      taskman_msg("Copied share for $pkg ($destination)...\n");
    }
  }
}

/**
 * @desc Create var folder
 */
function task_project_var_dir()
{
  $var_path = taskman_prop('PROJECT_DIR') . '/var';
  if(file_exists($var_path))
  {
    taskman_msg("Var dir ($var_path) exists...\n");
    return;
  }
  lmb_require('limb/fs/src/lmbFs.class.php');
  lmbFs::mkdir($var_path);
  taskman_msg("Created var dir ($var_path)...\n");
}

/**
 * @desc Init db
 * @deps project_db_create
 * @example project.php init_db -D DSN=mysqli://root:test@localhost/limb_app?charset=utf8
 */
function task_project_db_load($argv)
{
  require_once('limb/dbal/src/lmbDbDump.class.php');

  $code = <<<EOD
    \$toolkit = lmbToolkit :: instance();
    echo \$toolkit->getDefaultDbDSN()->toString();
EOD;
  $output = lmb_cli_run_code_in_project_env($code);
  $dsn = new lmbDbDSN($output);

  $conn = lmbToolkit :: instance()->createDbConnection($dsn);
  $dump_file = taskman_prop('PROJECT_DIR')
    . '/lib/limb/' . taskman_propor('INIT_PACKAGE','cms')
    . '/init/db.' . $conn->getType();
  $dump = new lmbDbDump($dump_file, $conn);
  $dump->load();
  taskman_msg("Dump ($dump_file) loaded...\n");
}

/**
 * @desc create db by specified DSN
 * @deps project_db_init_config
 * @example project.php create_db -D DSN=mysqli://root:test@localhost/limb_app?charset=utf8
 */
function task_project_db_create($args)
{
  lmb_package_require('dbal');
  lmb_require('limb/dbal/src/lmbDbDSN.class.php');

  $code = <<<EOD
    \$toolkit = lmbToolkit :: instance();
    echo \$toolkit->getDefaultDbDSN()->toString();
EOD;
  $output = lmb_cli_run_code_in_project_env($code);

  $dsn = new lmbDbDSN($output);
  $db_name = $dsn->database;
  $dsn->database = false;
  $conn = lmbToolkit::instance()->createDbConnection($dsn);
  lmbDBAL::execute('CREATE DATABASE '.$conn->quoteIdentifier($db_name), $conn);
  taskman_msg("Database ($db_name) created...\n");
}

/**
 *@desc Init db config by given DSN param
 *@example project.php init_db_config -D DSN=sqlite:///www/skel/db/database.sqlite
 */
function task_project_db_init_config()
{
  lmb_package_require('dbal');

  $config_file = taskman_prop('PROJECT_DIR').'/settings/db.conf.php';
  if(file_exists($config_file))
    return;

  taskman_msg("Database config does not exists...\n");

  if(!$dsn_str = taskman_propor('DSN', ''))
    $dsn_str = lmb_cli_ask_for_option('Dsn (example: mysqli://root:test@localhost/limb_app?charset=utf8)');

  lmb_require('limb/dbal/src/lmbDbDSN.class.php');
  $dsn = new lmbDbDSN($dsn_str);

  $config_text = "<?php\n\n \$conf = array('dsn' => '{$dsn_str}');";

  file_put_contents($config_file, $config_text);
  taskman_msg("DB config ($config_file) writed...Done\n");
}

function lmb_cli_run_code_in_project_env($internal_code)
{
  $tmp_file = tempnam(sys_get_temp_dir(), 'Limb');
  $code = "<?php\n"
   . "require_once '" . taskman_prop('PROJECT_DIR') . "/setup.php';\n"
   . $internal_code;
  file_put_contents($tmp_file, $code);
  $output = shell_exec("php ".$tmp_file);
  unlink($tmp_file);
  return $output;
}