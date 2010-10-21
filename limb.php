<?php
$limb_dir = dirname(__FILE__);

require_once($limb_dir.'/taskman/taskman.inc.php');

lmb_cli_init_limb($limb_dir);

taskman_propsetor('TASKS_MASKS', 'limb/*/cli/*.tasks.php' . PATH_SEPARATOR . 'src/*/cli/*.tasks.php');
if (null === taskman_propor('PROJECT_DIR', null))
taskman_propsetor('PROJECT_DIR', lmb_get_project_dir());
taskman_propsetor('LIMB_DIR', $limb_dir.'/../');

lmb_cli_init_tasks();
taskman_run();

function lmb_get_project_dir()
{
  if(!$project_dir = get_cfg_var('limb.project_dir'))
    $project_dir = lmb_cli_ask_for_option('Project directory', lmb_cli_find_project_dir(getcwd()));
  else
    echo "Project directory loaded from PHP config: {$project_dir}\n";
  return $project_dir;
}

function lmb_cli_find_project_dir($current_dir)
{
  if(file_exists($current_dir . '/setup.php'))
  {
  	ob_start();
  	register_shutdown_function('lmb_cli_check_limb_instance');
  	lmb_require($current_dir . '/setup.php');
  	ob_end_clean();
  	lmb_cli_check_limb_instance($disable = true);

    return $current_dir;
  }
  else
  {
    $parent_dir = dirname($current_dir);
    if($current_dir != $parent_dir)
      return lmb_cli_find_project_dir($parent_dir);
    else
      return getcwd();
  }
}

/**
 * Ugly hack for check situation when limb already included from another dir
 */
function lmb_cli_check_limb_instance($disable = false)
{
	static $is_disabled = false;
	if($disable)
	  $is_disabled = true;

  if($is_disabled || !$error = error_get_last())
    return;

  ob_end_clean();

  if(false !== strstr($error['message'], 'Cannot redeclare lmb_resolve_include_path()'))
  {
    taskman_sysmsg("You must use the same instance of Limb as in the project!\n");
    exit(1);
  }
}

function lmb_cli_init_limb($limb_dir)
{
  set_include_path(get_include_path() . PATH_SEPARATOR . $limb_dir . '/../');
  require_once('limb/core/common.inc.php');
}

function lmb_cli_init_tasks()
{
	foreach(explode(PATH_SEPARATOR, taskman_prop('TASKS_MASKS')) as $mask)
    lmb_require_glob($mask);
}

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