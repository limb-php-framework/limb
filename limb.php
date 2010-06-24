<?php
if(!$project_dir = get_cfg_var('limb.project_dir'))
  $project_dir = lmb_cli_ask_for_option('Project directory', lmb_cli_find_project_dir(getcwd()));
else
  echo "Project directory loaded from PHP config: {$project_dir}\n";

if(!$limb_dir = get_cfg_var('limb.dir'))
  $limb_dir = lmb_cli_ask_for_option('Limb directory', lmb_cli_find_limb_dir($project_dir));
else
  echo "Limb directory loaded from PHP config: {$limb_dir}\n";

if(!lmb_cli_init_tasks($limb_dir))
  die("ERROR: Limb not found".PHP_EOL);

lmb_require('limb/taskman/taskman.inc.php');

taskman_propset('PROJECT_DIR', $project_dir);
taskman_propset('LIMB_DIR', $limb_dir);
taskman_run();

function lmb_cli_ask_for_option($option_name, $default_value = '')
{
  if($default_value)
    $question_str = "{$option_name} [{$default_value}]: ";
  else
    $question_str = "{$option_name}: ";

  do
  {
    fputs(STDOUT, $question_str);
    if(!($user_in = trim(fgets(STDIN))) && $default_value)
      $user_in = $default_value;
  }
  while(!$user_in);

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
      return getcwd();
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

function lmb_cli_init_tasks($limb_dir)
{
  if(file_exists($limb_dir . '/limb/core/common.inc.php'))
  {
    if(!lmb_is_limb_included())
      set_include_path($limb_dir);
    require_once($limb_dir.'/limb/core/common.inc.php');
    lmb_require_glob($limb_dir . '/limb/*/cli/*.inc.php');
    return true;
  }
  return false;
}

function lmb_is_limb_included()
{
  return defined('LIMB_UNDEFINED');
}