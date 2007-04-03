<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbFunctionDelegate.class.php 5143 2007-02-20 21:40:01Z serega $
 * @package    classkit
 */
lmb_require('limb/classkit/src/lmbBaseDelegate.interface.php');

/**
* Object form of invoking a function
*/
class lmbFunctionDelegate implements lmbBaseDelegate
{
  /**
  * @var string Function name
  */
  protected $function;
  /**
  * @var string File path where function is defined
  */
  protected $file;

  /**
  * Constructor
  * @param string Function name
  * @param string File there function is defined
  */
  function __construct($function, $file = NULL)
  {
    $this->function = $function;
    $this->file = $file;
  }

  /**
  * Invokes function with $args
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

    return call_user_func_array($this->function, $args);
  }
}
?>
