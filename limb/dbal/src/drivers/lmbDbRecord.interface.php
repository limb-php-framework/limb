<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbDbRecord.interface.php 4994 2007-02-08 15:36:08Z pachanga $
 * @package    dbal
 */

interface lmbDbRecord extends ArrayAccess
{
  function get($name);
  function set($name, $value);//???
  function export();
  function import($values);
  function merge($values);
  function getInteger($name);
  function getFloat($name);
  function getString($name);
  function getBoolean($name);
  function getIntegerTimeStamp($name);
  function getStringDate($name);
  function getStringTime($name);
  function getStringTimeStamp($name);
  function getStringFixed($name);//???
  function getBlob($name);
}

?>
