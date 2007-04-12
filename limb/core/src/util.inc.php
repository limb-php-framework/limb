<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: util.inc.php 5143 2007-02-20 21:40:01Z serega $
 * @package    core
 */
//we put here useful stuff which hasn't been refactored and placed somewhere else...
lmb_require('limb/core/src/exception/lmbInvalidArgumentException.class.php');
lmb_require('limb/core/src/exception/lmbException.class.php');

/**
* Generates an unique object id using object class path and internal php object id
*/
function lmb_php_object_id($obj)
{
  if(!is_object($obj))
    throw new lmbInvalidArgumentException('object expected', array('arg' => $obj));

  $obj = lmbProxyResolver :: resolve($obj);

  $objId = (int)substr(strrchr("$obj", "#"), 1);

  if($objId <= 0)
    throw new lmbException('could not generate id for object', array('obj' => $obj));

  $class = (method_exists('getClass', $obj) ? $obj->getClass() : get_class($obj));
  return $class . $objId;
}

?>
