<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbAbstractTools.class.php 5446 2007-03-30 11:54:52Z serega $
 * @package    toolkit
 */
lmb_require('limb/toolkit/src/lmbToolkit.class.php');
lmb_require('limb/toolkit/src/lmbToolkitTools.interface.php');

/**
* Base class for most real applications tools
* @see lmbToolkit
*/
abstract class lmbAbstractTools implements lmbToolkitTools
{
  protected $reserved_methods = array('__construct', '_setRaw', '_getRaw');
  /**
  * @var lmbToolkit reference of lmbToolkit instance
  */
  protected $toolkit;

  function __construct()
  {
    $this->toolkit = lmbToolkit :: instance();
  }

  /**
  * Returns all methods of the childs classes except methods of lmbToolkitTools interface
  * @see lmbToolkitTools :: getToolsSignatures()
  */
  function getToolsSignatures()
  {
    $methods = get_class_methods(get_class($this));

    $signatures = array();
    foreach($methods as $method)
    {
      if(in_array($method, $this->reserved_methods))
        continue;
      $signatures[$method] = $this;
    }

    foreach(get_class_methods('lmbToolkitTools') as $method)
    {
      unset($signatures[$method]);
    }

    return $signatures;
  }

  protected function _setRaw($var, $value)
  {
    $this->toolkit->setRaw($var, $value);
  }

  protected function _getRaw($var)
  {
    return $this->toolkit->getRaw($var);
  }
}

?>
