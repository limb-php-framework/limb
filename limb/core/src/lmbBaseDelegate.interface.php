<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbBaseDelegate.interface.php 5143 2007-02-20 21:40:01Z serega $
 * @package    classkit
 */

/**
* Interface for defining delegates - a references to object method or functions that can be invoked.
*/
interface lmbBaseDelegate
{
  /**
  * Invokes callback. Calles object method, function, etc. depending of implementation
  * @param array Callback arguments
  * @return mixed
  */
  function invoke($args);
}
?>
