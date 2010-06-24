<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * @package core
 * @version $Id$
 */
function lmb_package_require($name, $packages_dir = '')
{
  if(!$packages_dir)
    $packages_dir = lmb_env_get('LIMB_PACKAGES_DIR');

  $main_file_path = $packages_dir . $name . '/common.inc.php';
  try {
    lmb_require($packages_dir . $name . '/common.inc.php');
  }
  catch(lmbException $e)
  {
    lmb_require('limb/core/src/exception/lmbNoSuchPackageException.class.php');
    throw new lmbNoSuchPackageException(
        "Package '{$name}' not found", array( 'name' => $name, 'dir' => $packages_dir, 'main_file' => $main_file_path)
    );
  }
}

function lmb_package_register($name, $package_dir)
{
  if(!isset($_ENV['LIMB_PACKAGES_INITED']))
    $_ENV['LIMB_PACKAGES_INITED'] = array();

  $_ENV['LIMB_PACKAGES_INITED'][$name] = rtrim($package_dir, '/');
}

function lmb_package_registered($name)
{
  return isset($_ENV['LIMB_PACKAGES_INITED'][$name]);
}

function lmb_package_get_path($name)
{
  if(!lmb_package_registered($name))
    throw new lmbNoSuchPackageException(
        "Package '{$name}' not registered", array( 'name' => $name)
    );
  return $_ENV['LIMB_PACKAGES_INITED'][$name];
}

function lmb_packages_list()
{
  if(!isset($_ENV['LIMB_PACKAGES_INITED']))
    return array();
  else
    return $_ENV['LIMB_PACKAGES_INITED'];
}

function lmb_require_package_class($package, $path_in_package_src)
{
  $file_path = lmb_package_get_path($package).'/src/'.$path_in_package_src.'.class.php';
  lmb_require($file_path);
}
