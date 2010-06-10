<?php
$current_dir = lmb_cli_find_project_dir(getcwd());
$project_dir = lmb_cli_ask_for_option('Project directory', $current_dir);

$limb_dir = lmb_cli_ask_for_option('Limb directory', lmb_cli_find_limb_dir($project_dir));
if(!lmb_cli_init_limb($limb_dir))
  die("ERROR: Limb not found".PHP_EOL);

lmb_require('limb/taskman/taskman.inc.php');

taskman_propset('PROJECT_DIR', $project_dir);
taskman_propset('LIMB_DIR', $limb_dir);
taskman_run();

function lmb_cli_ask_for_option($option_name, $default_value)
{
  fputs(STDOUT, "{$option_name} [{$default_value}]: ");
  if(!$user_in = trim(fgets(STDIN)))
    $user_in = $default_value;
  return $user_in;
}

function lmb_cli_ask_for_accept($question)
{
  do
  {
    fputs(STDOUT, "{$question} [y/n]: ");
    $user_in = trim(fgets(STDIN));
  }
  while($user_in != 'y' && $user_in != 'n');

  return $user_in == 'y' ? true : false;
}

function lmb_cli_find_project_dir($current_dir)
{
  if(file_exists($current_dir . '/setup.php'))
    return $current_dir;
  else
  {
    $parent_dir = dirname($current_dir);
    if($current_dir != $parent_dir)
      return lmb_cli_find_project_dir($parent_dir);
    else
      return '';
  }
}

function lmb_cli_find_limb_dir($project_dir)
{
  if(file_exists($project_dir . '/setup.php'))
    require_once($project_dir . '/setup.php');
  foreach(explode(PATH_SEPARATOR, get_include_path()) as $path)
  {
    if(file_exists($path . '/limb/core/common.inc.php'))
      return $path;
  }
  return '';
}

function lmb_cli_init_limb($limb_dir)
{
  if(file_exists($limb_dir . '/limb/core/common.inc.php'))
  {
    set_include_path($limb_dir);
    require_once('limb/core/common.inc.php');
    lmb_require_glob($limb_dir . '/limb/*/cli/*.inc.php');
    return true;
  }
  return false;
}