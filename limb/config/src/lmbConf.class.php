<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbConf.class.php 5429 2007-03-29 14:55:34Z pachanga $
 * @package    config
 */
lmb_require('limb/datasource/src/lmbDataspace.class.php');

class lmbConf extends lmbDataspace implements Iterator
{
  protected $current;
  protected $valid = false;

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

  function valid()
  {
    return $this->valid;
  }

  function current()
  {
    return $this->current;
  }

  function next()
  {
    $this->current = next($this->properties);
    $this->valid = $this->current !== false;
  }

  function rewind()
  {
    $this->current = reset($this->properties);
    $this->valid = $this->current !== false;
  }

  function key()
  {
    return key($this->properties);
  }
}
?>