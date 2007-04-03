<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbDatasource.interface.php 4992 2007-02-08 15:35:40Z pachanga $
 * @package    datasource
 */

interface lmbDatasource
{
  function get($name);
  function getByPath($name);
  function set($name, $value);
  function setByPath($name, $value);
  function remove($name);
  function reset();
  function export();
  function hasProperty($name);
}

?>