<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbHandle.class.php 4987 2007-02-08 15:35:15Z pachanga $
 * @package    classkit
 */
lmb_require(dirname(__FILE__) . '/lmbProxy.class.php');

class lmbHandle extends lmbProxy
{
  protected $args;
  protected $class_path;
  protected $class;

  function __construct($class_path, $args = array(), $class = null)
  {
    if(is_null($class))
      $this->class = $this->_getClassName($class_path);
    else
      $this->class = $class;

    $this->class_path = $class_path;
    $this->args = $args;
  }

  function isHandle()
  {
    return true;
  }

  function getClass()
  {
    return $this->class;
  }

  protected function _createOriginalObject()
  {
    lmb_require($this->_getClassFullPath());

    $refl = new ReflectionClass($this->class);
    return call_user_func_array(array($refl, 'newInstance'),$this->args);
  }

  protected function _getClassName($class_path)
  {
    $items = explode('.', end(explode('/', $class_path)));
    return $items[0];
  }

  protected function _getClassFullPath()
  {
    if(substr($this->class_path, -4, 4) != '.php')
      return $this->class_path . '.class.php';
    else
      return $this->class_path;
  }
}
?>