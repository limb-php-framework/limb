<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbSetInterface.interface.php 5558 2007-04-06 13:02:07Z pachanga $
 * @package    datasource
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