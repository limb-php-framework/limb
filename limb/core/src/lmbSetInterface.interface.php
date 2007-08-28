<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * interface lmbSetInterface.
 *
 * @package core
 * @version $Id$
 */
interface lmbSetInterface extends ArrayAccess
{
  function get($name);
  function set($name, $value);
  function remove($name);
  function reset();
  function export();
  function import($values);
  function has($name);
}

?>
