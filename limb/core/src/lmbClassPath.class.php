<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbClassPath.class.php 5143 2007-02-20 21:40:01Z serega $
 * @package    classkit
 */
lmb_require('limb/core/src/lmbHandle.class.php');

class lmbClassPath
{
  protected $class_name;
  protected $raw_path;

  function __construct($raw_path)
  {
    if(is_string($raw_path))
      $this->raw_path = $this->_parseConstants($raw_path);

    $this->_initClassName();
  }

  function getClassName()
  {
    return $this->class_name;
  }

  static function create($path, $args = array())
  {
    $class_path = new lmbClassPath($path);
    return $class_path->createObject($args);
  }

  function import()
  {
    lmb_require($this->raw_path . '.class.php');
  }

  function createHandle($args = array())
  {
    return new lmbHandle($this->raw_path, $args);
  }

  function createObject($args = array())
  {
    if(!class_exists($this->class_name))
      require_once($this->_getClassFileFullPath());

    $refl = new ReflectionClass($this->class_name);
    return call_user_func_array(array($refl, 'newInstance'),$args);
  }

  protected function _getClassFileFullPath()
  {
    return $this->raw_path . '.class.php';
  }

  protected function _parseConstants($value)
  {
    return preg_replace('~\{([^\}]+)\}~e', "constant('\\1')", $value);
  }

  protected function _initClassName()
  {
    if(!$this->raw_path)
      throw new lmbException("Invalid class path: {$this->raw_path}");

    $this->class_name = end(explode('/', $this->raw_path));
  }
}

?>
