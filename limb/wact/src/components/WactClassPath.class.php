<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * class WactClassPath.
 *
 * @package wact
 * @version $Id$
 */
class WactClassPath
{
  protected $class_name;
  protected $include_path;

  function __construct($raw_path_or_class_name, $include_path = '')
  {
    $raw_path_or_class_name = $this->_parseConstants($raw_path_or_class_name);

    if($include_path)
      $this->include_path = $include_path;
    elseif(WactTemplate :: isFileReadable($raw_path_or_class_name . '.class.php'))
      $this->include_path = $raw_path_or_class_name . '.class.php';

    $this->class_name = end(explode('/', $raw_path_or_class_name));
  }

  function createObject($args = array())
  {
    if(!class_exists($this->class_name) && $this->include_path)
      require_once($this->include_path);

    $refl = new ReflectionClass($this->class_name);
    return call_user_func_array(array($refl, 'newInstance'),$args);
  }

  protected function _parseConstants($value)
  {
    return preg_replace('~\{([^\}]+)\}~e', "constant('\\1')", $value);
  }
}


