<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbConf.class.php 5628 2007-04-11 12:09:20Z pachanga $
 * @package    config
 */
lmb_require('limb/datasource/src/lmbSet.class.php');

class lmbConf extends lmbSet
{
  function __construct($file)
  {
    $conf = array();

    if(!@include($file))
      throw new lmbException("Config file '$file' not found");

    if($override_file = $this->_getOverrideFile($file))
    {
      $original = $conf;
      include($override_file);
      $conf = array_merge($original, $conf);
    }
    parent :: __construct($conf);
  }

  protected function _getOverrideFile($file_path)
  {
    $file_name = substr($file_path, 0, strpos($file_path, '.php'));
    $override_file_name = $file_name . '.override.php';

    if(file_exists($override_file_name))
      return $override_file_name;
    else
      return false;
  }
}
?>