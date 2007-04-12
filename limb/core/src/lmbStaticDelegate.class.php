<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbStaticDelegate.class.php 5143 2007-02-20 21:40:01Z serega $
 * @package    core
 */
lmb_require('limb/core/src/lmbBaseDelegate.interface.php');

/**
* Object form of invoking a class method
*/
class lmbStaticDelegate implements lmbBaseDelegate
{
  /**
  * @var string File path where function is defined
  */
  protected $file;
  /**
  * @var string Method name to call
  */
  protected $method;
  /**
  * @var string Class name
  */
  protected $class;

  /**
  * Constructor
  * @param string Class name
  * @param string Method name to call
  * @param string File path where function is defined
  */
  function __construct($class, $method, $file = NULL)
  {
    $this->class = $class;
    $this->method = $method;
    $this->file = $file;
  }

  /**
  * Invokes class method with $args
  * Includes file if $file is not empty
  * @see lmbBaseDelegate :: invoke
  */
  function invoke($args)
  {
    if (!is_null($this->file))
    {
      lmb_require($this->file);
      $this->file = NULL;
    }
    return call_user_func_array(array($this->class, $this->method), $args);
  }
}
?>
